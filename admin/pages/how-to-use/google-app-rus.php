<div class="onp-help-section">
	<h1><?php _e('Getting Google Client ID', 'bizpanda'); ?></h1>

	<p>
		<?php _e('A Google Client ID is required for the following buttons:', 'bizpanda'); ?>
	<ul>
		<li><?php _e('YouTube Subscribe of the Social Locker.', 'bizpanda') ?></li>
		<li><?php _e('Google Sign-In of the Sign-In Locker.', 'bizpanda') ?></li>
		<?php if( BizPanda::hasPlugin('optinpanda') ) { ?>
			<li><?php _e('Google Subscribe of the Email Locker.', 'bizpanda') ?></li>
		<?php } ?>
	</ul>
	</p>
	<p><?php _e('If you want to use these buttons, you need to get Google Client ID App for your website.', 'bizpanda') ?>
		<?php _e('<strong>You don\'t need to get a Client ID</strong> if you\'re not going to use these buttons.', 'bizpanda') ?></p>
</div>
<div class="onp-help-section">
	<p><?php printf(__('1. Go to the <a href="%s" target="_blank">Google Developers Console</a>.', 'bizpanda'), 'https://console.developers.google.com/apis/') ?></p>
</div>
<div class="onp-help-section">
	<p>2. Нажмите на закладку "Выбрать проект (Api project)", если у вас уже есть проекты, выберите созданный или
		нажмите кнопку "Создать проект (Create project)"</p>

	<p class='onp-img'>
		<img src='http://cconp.s3.amazonaws.com/sociallocker/rus/google/screen-1.jpg'/>
	</p>

	<p class='onp-img'>
		<img src='http://cconp.s3.amazonaws.com/sociallocker/rus/google/screen-2.jpg'/>
	</p>

	<p class='onp-img'>
		<img src='http://cconp.s3.amazonaws.com/sociallocker/rus/google/screen-3.jpg'/>
	</p>
</div>
<div class="onp-help-section">
	<p>3. Wait until your new project is created. After that you will be automatically redirected to your project
		dashboard.</p>
</div>
<div class="onp-help-section">
	<p>4. Включите API</p>

	<p class='onp-img'>
		<img src='http://cconp.s3.amazonaws.com/sociallocker/rus/google/screen-4.jpg'/>
	</p>

	<p>Перейдите сначала по ссылке Google+ API и нажмите кнопку включить.</p>

	<p class='onp-img'>
		<img src='http://cconp.s3.amazonaws.com/sociallocker/rus/google/screen-5.jpg'/>
	</p>

	<p>Сдейте тоже самое для Yotube Data Api</p>
</div>
<div class="onp-help-section">
	<p>5. Нажмите на закладку "Учетные записи"</p>

	<p class='onp-img'>
		<img src='http://cconp.s3.amazonaws.com/sociallocker/rus/google/screen-6.jpg'/>
	</p>

	<p>6. Выберите закладку "Окно запроса доступа oAuth" и настройте его по образцу (используйте ваши данные).</p>

	<p class='onp-img'>
		<img src='http://cconp.s3.amazonaws.com/sociallocker/rus/google/screen-7.jpg'/>
	</p>

	<p>После сохранения вас отправят снова на закладку "Учетные данные"</p>
</div>
<div class="onp-help-section">
	<p>7. Нажмите кнопку "Создать учетные данные" и выберите из списка "Идентификатор клиента oAuth"</p>

	<p class='onp-img'>
		<img src='http://cconp.s3.amazonaws.com/sociallocker/rus/google/screen-8.jpg'/>
	</p>

	<p class='onp-img'>
		<img src='http://cconp.s3.amazonaws.com/sociallocker/rus/google/screen-9.jpg'/>
	</p>
</div>

<?php
	$origin = null;
	$pieces = parse_url(site_url());
	$domain = isset($pieces['host'])
		? $pieces['host']
		: '';
	if( preg_match('/(?P<domain>[a-z0-9][a-z0-9\-]{1,63}\.[a-z\.]{2,6})$/i', $domain, $regs) ) {
		$origin = $regs['domain'];
	}
?>

<div class="onp-help-section">
	<p>Появится форма создания идентификатора клиента. Заполните форму используя данные из таблицы ниже.</p>
	<table class="table">
		<thead>
		<tr>
			<th>Поле</th>
			<th>Как заполнить</th>
		</tr>
		</thead>
		<tbody>
		<tr>
			<td class="onp-title">Тип приложения</td>
			<td>
				<p>Веб-приложение</p>
			</td>
		</tr>
		<tr>
			<td class="onp-title">Разрешенные источники JavaScript</td>
			<td>
				<p>Добавьте следующие домены (скопируйте и вставьте один домен в форму и нажмите мышкой мимо поля, затем
					следующий):</p>

				<p><i><?php echo 'http://' . str_replace('www.', '', $origin) ?></i></p>

				<p><i><?php echo 'http://www.' . $origin ?></i></p>
			</td>
		</tr>
		<tr>
			<td class="onp-title">Разрешенные URI перенаправления</td>
			<td>
				<p>Paste the URL:</p>

				<p><i><?php echo add_query_arg(array(
							'action' => 'opanda_connect',
							'opandaHandler' => 'google'
						), admin_url('admin-ajax.php')) ?></i>
				</p>
			</td>
		</tr>
		</tbody>
	</table>
	<p class='onp-img'>
		<img src='http://cconp.s3.amazonaws.com/sociallocker/rus/google/screen-10.jpg'/>
	</p>
</div>
<div class="onp-help-section">
	<p>8. После нажатия на кнопку "Создать" появится окно с индентификатором клиента.</p>

	<p class='onp-img'>
		<img src='http://cconp.s3.amazonaws.com/sociallocker/rus/google/screen-11.jpg'/>
	</p>
</div>
<div class="onp-help-section">
	<p><?php printf('9. Копируйте ID клиента и вставьте в Общие настройки > <a href="%s">Настройки социальных кнопок</a>.', opanda_get_settings_url('social')) ?></p>

	<p class='onp-img'>
		<img src='http://cconp.s3.amazonaws.com/sociallocker/rus/google/screen-12.jpg'/>
	</p>
</div>
<div class="onp-help-section">
	<h3>Видео пример получения ID клиента</h3>
	<iframe width="663" height="415" src="https://www.youtube.com/embed/KjF0cs-2ySA" frameborder="0"
	        allowfullscreen></iframe>
</div>