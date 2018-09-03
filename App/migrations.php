<?php

namespace App;

use \Core\Foundation\Application;
use \Core\Database\Migration\Migration;

$config = Application::$instance->dependencies->get('Config');

if (strtolower($config->env) != 'dev') {
    return;
}

// clients
$clients = Migration::table('clients');
$clients->add('id')->id();
$clients->add('email')->string()->unique();
$clients->add('password')->string();
$clients->add('created_at')->dateCreate();
$clients->add('updated_at')->dateUpdate();

// subscriptions
$subscriptions = Migration::table('subscriptions');
$subscriptions->add('id')->id();

// payments
$payments = Migration::table('payments');
$payments->add('id')->id();

// oauth clients
$oauthPartner = Migration::table('oauth_partners');
$oauthPartner->add('id')->id();
$oauthPartner->add('slug')->string(255)->unique();
$oauthPartner->add('name')->string(255);
$oauthPartner->add('desc')->text();
$oauthPartner->add('redirect_url')->string(255);

$oauthTokens = Migration::table('oath_tokens');
$oauthTokens->add('id')->id();
$oauthTokens->add('client_id')->id(false)->relation('clients', 'id');
$oauthTokens->add('oauth_partner_id')->id(false)->relation('oauth_partners', 'id');
$oauthTokens->add('token_type')->string(255);
$oauthTokens->add('token')->string(255)->unique();
$oauthTokens->add('date_expiration')->timestamp();
$oauthTokens->add('created_at')->dateCreate();
$oauthTokens->add('updated_at')->dateUpdate();

// administrations
$administrations = Migration::table('administrations');
$administrations->add('id')->id();
$administrations->add('client_id')->id(false)->relation('clients', 'id');
$administrations->add('code')->int(11);
$administrations->add('name')->string();
$administrations->add('created_at')->dateCreate();
$administrations->add('updated_at')->dateUpdate();

// permissions
$permissions = Migration::table('permissions');
$permissions->add('id')->id();
$permissions->add('administration_id')->id(false)->relation('administrations', 'id');
$permissions->add('key')->string();
$permissions->add('value')->string();
$permissions->add('created_at')->dateCreate();
$permissions->add('updated_at')->dateUpdate();

/*
for later
*/
$invoices = Migration::table('invoices');
$invoices->add('id')->id();

$invoiceLine = Migration::table('invoice_lines');
$invoiceLine->add('id')->id();

$customers = Migration::table('customers');
$customers->add('id')->id();

$addresses = Migration::table('addresses');
$addresses->add('id')->id();

// accounts
$accounts = Migration::table('accounts');
$accounts->add('id')->id();
$accounts->add('administration_id')->id(false)->relation('administrations', 'id');
$accounts->add('name')->string();
$accounts->add('code')->int(11);
$accounts->add('created_at')->dateCreate();
$accounts->add('updated_at')->dateUpdate();

// journals
$journals = Migration::table('journals');
$journals->add('id')->id();
$journals->add('administration_id')->id(false)->relation('administrations', 'id');
$journals->add('created_at')->dateCreate();
$journals->add('updated_at')->dateUpdate();

// balances
$balances = Migration::table('balances');
$balances->add('id')->id();
$balances->add('administration_id')->id(false)->relation('administrations', 'id');
$balances->add('created_at')->dateCreate();
$balances->add('updated_at')->dateUpdate();

// lines
$lines = Migration::table('lines');
$lines->add('id')->id();
$lines->add('account_id')->id(false)->relation('accounts', 'id');
$lines->add('parent_type')->string()->index();
$lines->add('parent_id')->id(false);
$lines->add('credit')->int(11)->unsigned();
$lines->add('debit')->int(11)->unsigned();
$lines->add('created_at')->dateCreate();
$lines->add('updated_at')->dateUpdate();
