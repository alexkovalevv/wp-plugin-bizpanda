<div class="onp-help-section">
	<h1><?php _e('Creating Facebook App', 'bizpanda'); ?></h1>

	<p>
		<?php _e('A Facebook App is required for the following buttons:', 'bizpanda'); ?>
	<ul>
		<?php if( BizPanda::hasPlugin('sociallocker') ) { ?>
			<li><?php _e('Facebook Share of the Social Locker.', 'bizpanda') ?></li>
		<?php } ?>
		<li><?php _e('Facebook Sign-In of the Sign-In Locker.', 'bizpanda') ?></li>
		<?php if( BizPanda::hasPlugin('optinpanda') ) { ?>
			<li><?php _e('Facebook Subscribe of the Email Locker.', 'bizpanda') ?></li>
		<?php } ?>
	</ul>
	</p>
	<p><?php _e('If you want to use these buttons, you need to register a Facebook App for your website. Otherwise you can use the default Facebook App Id (117100935120196).', 'bizpanda') ?></p>

	<p><?php _e('In other words, <strong>you don\'t need to create an own app</strong> if you\'re not going to use these Facebook buttons.', 'bizpanda') ?></p>
</div>
<div class="onp-help-section">
	<p><?php printf('1. Перейдите по ссылке </са> <a href="%s" target="_blank">developers.facebook.com</a> и нажмите "Мои приложения" - "Добавить новое приложение":', 'https://developers.facebook.com/') ?></p>

	<p class='onp-img'>
		<img src='http://cconp.s3.amazonaws.com/sociallocker/rus/facebook/screen-1.jpg'/>
	</p>
</div>
<div class="onp-help-section">
	<p>2. Когда вы увидете всплывающее окно, заполните форму по образцу и нажмите "Создать ID приложения</p>

	<p class='onp-img'>
		<img src='http://cconp.s3.amazonaws.com/sociallocker/rus/facebook/screen-2.jpg'/>
	</p>

	<p>Пройдите проверку для роботов.</p>

	<p class='onp-img'>
		<img src='http://cconp.s3.amazonaws.com/sociallocker/rus/facebook/screen-3.jpg'/>
	</p>
</div>
<div class="onp-help-section">
	<p>4. Перейдите на закладку "Настройки":</p>

	<p class='onp-img'>
		<img src='http://cconp.s3.amazonaws.com/sociallocker/rus/facebook/screen-4.jpg'/>
	</p>
</div>
<div class="onp-help-section">
	<p>5. В поле "Домены приложений" введите основной домен и все поддомены на которых будет использоваться плагин.
		"Пространство имен" должно быть любым набором латинских символов в нижнем регистре.
		Адрес "политики конфиденциальности" и "пользовательского соглашения" возьмите из таблицы ниже. Когда вы
		заполните все поля, нажмите кнопку "Добавить платформу".</p>
	<table class="table">
		<thead>
		<tr>
			<th>Поле</th>
			<th>Как заполнить</th>
		</tr>
		</thead>
		<tbody>
		<tr>
			<td>Политика конфиденциальности</td>
			<td><?= site_url(); ?>/?bizpanda=privacy-policy</td>
		</tr>
		<tr>
			<td>Пользовательское соглашения</td>
			<td><?= site_url(); ?>/?bizpanda=terms-of-use</td>
		</tr>
		</tbody>
	</table>
	<p class='onp-img'>
		<img src='http://cconp.s3.amazonaws.com/sociallocker/rus/facebook/screen-5.jpg'/>
	</p>
</div>
<div class="onp-help-section">
	<p>6. Далее в появившемся окне выберите тип "Веб-сайт":</p>

	<p class='onp-img'>
		<img src='http://cconp.s3.amazonaws.com/sociallocker/rus/facebook/screen-6.jpg'/>
	</p>
</div>
<div class="onp-help-section">
	<p>7. Укажите основной url своего сайта и сохраните изменения.</p>

	<p class='onp-img'>
		<img src='http://cconp.s3.amazonaws.com/sociallocker/rus/facebook/screen-7.jpg'/>
	</p>
</div>
<div class="onp-help-section">
	<p>8. Перейдите в раздел "Проверка приложения" и сделайте ваше приложение публичным. Нажмите переключатель, чтобы
		было слово "Да".</p>

	<p class='onp-img'>
		<img src='http://cconp.s3.amazonaws.com/sociallocker/rus/facebook/screen-8.jpg'/>
	</p>
</div>
<div class="onp-help-section">
	<p>9. Перейдите в раздел "Вход через Facebook" и выберите закладку "Настройки". Заполните поле "Действительные
		URL-адреса для перенаправления OAuth" взяв url из таблицы ниже.</p>
	<table class="table">
		<thead>
		<tr>
			<th>Поле</th>
			<th>Как заполнить</th>
		</tr>
		</thead>
		<tbody>
		<tr>
			<td>Действительные URL-адреса для перенаправления OAuth" взяв url из таблицы ниже.</td>
			<td><?php echo add_query_arg(array(
					'action' => 'opanda_connect',
					'opandaHandler' => 'facebook'
				), admin_url('admin-ajax.php')) ?></td>
		</tr>
		</tbody>
	</table>
	<p class='onp-img'>
		<img src='http://cconp.s3.amazonaws.com/sociallocker/rus/facebook/screen-9.jpg'/>
	</p>
</div>
<div class="onp-help-section">
	<p>10. Перейдите в раздел "Панель" и скопируйте ID приложения в facebook.</p>

	<p class='onp-img'>
		<img src='http://cconp.s3.amazonaws.com/sociallocker/rus/facebook/screen-10.jpg'/>
	</p>
</div>
<div class="onp-help-section">
	<p><?php printf('9. Вставьте ID приложения в Общие настройки > <a href="%s">Настройки социальных кнопок</a>.', opanda_get_settings_url('social')) ?></p>

	<p class='onp-img'>
		<img src='http://cconp.s3.amazonaws.com/sociallocker/rus/facebook/screen-11.jpg'/>
	</p>
</div>
<div class="onp-help-section">
	<h4>Видео, как зарегистрировать приложение в facebook</h4>
	<iframe width="670" height="450" src="https://www.youtube.com/embed/hc6gS3cE1LI" frameborder="0"
	        allowfullscreen></iframe>
</div>

