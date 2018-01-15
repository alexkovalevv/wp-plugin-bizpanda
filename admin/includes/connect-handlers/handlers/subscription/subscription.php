<?php
	if( !defined('OPANDA_PROXY') ) {
		exit;
	}

	/**
	 * The class to proxy the request to the Subscription API.
	 */
	class OPanda_SubscriptionHandler extends OPanda_Handler {

		/**
		 * Handles the proxy request.
		 */
		public function handleRequest()
		{

			if( !isset($_POST['opandaRequestType']) || !isset($_POST['opandaService']) ) {
				throw new Opanda_HandlerInternalException('Invalid request. The "opandaRequestType" or "opandaService" are not defined.');
			}

			require_once OPANDA_BIZPANDA_DIR . '/admin/includes/subscriptions.php';
			$service = OPanda_SubscriptionServices::getCurrentService();

			if( empty($service) ) {
				throw new Opanda_HandlerInternalException(sprintf('The subscription service is not set.'));
			}

			// - service name

			$serviceName = $this->options['service'];
			if( $serviceName !== $service->name ) {
				throw new Opanda_HandlerInternalException(sprintf('Invalid subscription service "%s".', $serviceName));
			}

			// - request type

			$requestType = strtolower($_POST['opandaRequestType']);
			$allowed = array('check', 'subscribe');

			if( !in_array($requestType, $allowed) ) {
				throw new Opanda_HandlerInternalException(sprintf('Invalid request. The action "%s" not found.', $requestType));
			}

			// - identity data

			$identityData = isset($_POST['opandaIdentityData'])
				? $_POST['opandaIdentityData']
				: array();
			$identityData = $this->normilizeValues($identityData);

			if( empty($identityData['email']) ) {
				throw new Opanda_HandlerException('Unable to subscribe. The email is not specified.');
			}

			// - service data

			$serviceData = isset($_POST['opandaServiceData'])
				? $_POST['opandaServiceData']
				: array();
			$serviceData = $this->normilizeValues($serviceData);

			// - context data

			$contextData = isset($_POST['opandaContextData'])
				? $_POST['opandaContextData']
				: array();
			$contextData = $this->normilizeValues($contextData);

			// - list id

			$listId = isset($_POST['opandaListId'])
				? $_POST['opandaListId']
				: null;
			if( empty($listId) ) {
				throw new Opanda_HandlerException('Unable to subscribe. The list ID is not specified.');
			}

			// - double opt-in

			$doubleOptin = isset($_POST['opandaDoubleOptin'])
				? $_POST['opandaDoubleOptin']
				: true;
			$doubleOptin = $this->normilizeValue($doubleOptin);

			// - confirmation

			$confirm = isset($_POST['opandaConfirm'])
				? $_POST['opandaConfirm']
				: true;
			$confirm = $this->normilizeValue($confirm);

			// verifying user data if needed while subscribing
			// works for social subscription

			$verified = false;
			$mailServiceInfo = OPanda_SubscriptionServices::getServiceInfo();
			$modes = $mailServiceInfo['modes'];

			if( 'subscribe' === $requestType ) {

				if( $doubleOptin && in_array('quick', $mailServiceInfo['modes']) ) {
					$verified = $this->verifyUserData($identityData, $serviceData);
				}
			}

			// prepares data received from custom fields to be transferred to the mailing service

			$itemId = intval($contextData['itemId']);

			$identityData = $this->prepareDataToSave($service, $itemId, $identityData);
			$serviceReadyData = $this->mapToServiceIds($service, $itemId, $identityData);
			$identityData = $this->mapToCustomLabels($service, $itemId, $identityData);

			// checks if the subscription has to be procces via WP

			$subscribeMode = get_post_meta($itemId, 'opanda_subscribe_mode', true);
			$subscribeDelivery = get_post_meta($itemId, 'opanda_subscribe_delivery', true);

			$isWpSubscription = false;

			if( $service->hasSingleOptIn() && in_array($subscribeMode, array(
					'double-optin',
					'quick-double-optin'
				)) && ($service->isTransactional() || $subscribeDelivery == 'wordpress')
			) {

				$isWpSubscription = true;
			}

			// creating subscription service

			try {

				$result = array();

				if( 'subscribe' === $requestType ) {

					if( $isWpSubscription ) {

						// if the use signes in via a social network and we managed to confirm that the email is real,
						// then we can skip the confirmation process

						if( $verified ) {
							OPanda_Leads::add($identityData, $contextData, true, true);

							return $service->subscribe($serviceReadyData, $listId, false, $contextData, $verified);
						} else {
							$result = $service->wpSubscribe($identityData, $serviceReadyData, $contextData, $listId, $verified);
						}
					} else {
						$result = $service->subscribe($serviceReadyData, $listId, $doubleOptin, $contextData, $verified);
					}

					$status = ($result && isset($result['status']))
						? $result['status']
						: 'error';

					//todo: хук является устаревшим opanda_subscribe
					factory_000_do_action_deprecated('opanda_subscribe', array(
						$status,
						$identityData,
						$contextData,
						$isWpSubscription
					), '1.2.4', 'bizpanda_user_subscribe');

					do_action('bizpanda_user_subscribe', $status, $identityData, $contextData, $isWpSubscription);
				} elseif( 'check' === $requestType ) {

					if( $isWpSubscription ) {
						$result = $service->wpCheck($identityData, $serviceReadyData, $contextData, $listId, $verified);
					} else {
						$result = $service->check($serviceReadyData, $listId, $contextData);
					}

					$status = ($result && isset($result['status']))
						? $result['status']
						: 'error';

					//todo: хук является устаревшим opanda_check
					factory_000_do_action_deprecated('opanda_check', array(
						$status,
						$identityData,
						$contextData,
						$isWpSubscription
					), '1.2.4', 'bizpanda_check_user_subsribe');

					do_action('bizpanda_check_user_subsribe', $status, $identityData, $contextData, $isWpSubscription);
				}

				//todo: Фильтр opanda_subscription_result устарел
				$result = factory_000_apply_filters_deprecated("opanda_subscription_result", array(
					$result,
					$identityData
				), '1.2.4', 'bizpanda_subscription_result');
				$result = apply_filters('bizpanda_subscription_result', $result, $identityData);

				if( !defined('OPANDA_WORDPRESS') ) {
					return $result;
				}

				// calls the hook to save the lead in the database
				if( $result && isset($result['status']) ) {

					$actionData = array(
						'identity' => $identityData,
						'requestType' => $requestType,
						'service' => $this->options['service'],
						'list' => $listId,
						'doubleOptin' => $doubleOptin,
						'confirm' => $confirm,
						'context' => $contextData
					);

					if( 'subscribed' === $result['status'] ) {

						//todo: хук является устаревшим opanda_subscribed
						factory_000_do_action_deprecated('opanda_subscribed', array($actionData), '1.2.4', 'bizpanda_user_subscribed');

						do_action('bizpanda_user_subscribed', $actionData);
					} else {
						//todo: хук является устаревшим opanda_pending
						factory_000_do_action_deprecated('opanda_pending', array($actionData), '1.2.4', 'bizpanda_pedding_subscribe');

						do_action('bizpanda_pedding_subscribe', $actionData);
					}
				}

				return $result;
			} catch( Exception $ex ) {
				throw new Opanda_HandlerException($ex->getMessage());
			}
		}
	}
