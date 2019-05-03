{*
* 2007-2019 PrestaShop
*
* NOTICE OF LICENSE
*
* This source file is subject to the Academic Free License (AFL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/afl-3.0.php
* If you did not receive a copy of the license and are unable to
* obtain it through the world-wide-web, please send an email
* to license@prestashop.com so we can send you a copy immediately.
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade PrestaShop to newer
* versions in the future. If you wish to customize PrestaShop for your
* needs please refer to http://www.prestashop.com for more information.
*
*  @author    PrestaShop SA <contact@prestashop.com>
*  @copyright 2007-2019 PrestaShop SA
*  @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*}

<div class="panel">
	<h3><i class="icon icon-eraser"></i> {l s='Clear Cache Cron Task Scheduled' mod='clearcachecrontask'}</h3>
	<p>
		<strong>{l s='How to use ?' mod='clearcachecrontask'}</strong>
	</p>
	<p>
		{l s='You just have to setup your cron task manager (prestashop plugin like "Cron tasks manager", or directly in your server configuration) to call the following URL, at the frequency of your choice :' mod='clearcachecrontask'}
	</p>
	<p>
		<a href="{$url|escape:'htmlall':'UTF-8'}?token={$token|escape:'htmlall':'UTF-8'}" target="_blank">
			{$url|escape:'htmlall':'UTF-8'}?token={$token|escape:'htmlall':'UTF-8'}
		</a>
	</p>
</div>