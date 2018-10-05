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
$clients->add('email')->string(255)->unique();
$clients->add('password')->string(255);
$clients->add('subscription')->string(25)->default('none');
$clients->add('role')->string(25)->default('client');
$clients->add('createdAt')->dateCreate();
$clients->add('updatedAt')->dateUpdate();

// login sessions
$sessions = Migration::table('sessions');
$sessions->add('id')->id();
$sessions->add('clientId')->id(false)->relation('clients', 'id');
$sessions->add('ip')->string(255);
$sessions->add('authorization')->text();
$sessions->add('expiry')->timestamp();
$sessions->add('createdAt')->dateCreate();
$sessions->add('updatedAt')->dateUpdate();

// oauth clients
$oauthPartner = Migration::table('oauthPartners');
$oauthPartner->add('id')->id();
$oauthPartner->add('clientId')->id(false)->relation('clients', 'id');
$oauthPartner->add('slug')->string(255)->unique();
$oauthPartner->add('name')->string(255);
$oauthPartner->add('secret')->string(25)->unique();
$oauthPartner->add('desc')->text()->nullable();
$oauthPartner->add('redirectUrl')->string(255)->nullable();

// oauth tokens
$oauthTokens = Migration::table('oauthTokens');
$oauthTokens->add('id')->id();
$oauthTokens->add('clientId')->id(false)->relation('clients', 'id');
$oauthTokens->add('oauthPartnerId')->id(false)->relation('oauthPartners', 'id');
$oauthTokens->add('tokenType')->string(255);
$oauthTokens->add('token')->text();
$oauthTokens->add('expiry')->timestamp();
$oauthTokens->add('createdAt')->dateCreate();
$oauthTokens->add('updatedAt')->dateUpdate();

// administrations
$administrations = Migration::table('administrations');
$administrations->add('id')->id();
$administrations->add('clientId')->id(false)->relation('clients', 'id');
$administrations->add('code')->int(11);
$administrations->add('name')->string();
$administrations->add('createdAt')->dateCreate();
$administrations->add('updatedAt')->dateUpdate();

// permissions
$permissions = Migration::table('permissions');
$permissions->add('id')->id();
$permissions->add('clientId')->id(false)->relation('clients', 'id');
$permissions->add('parentType')->id(false)->index();
$permissions->add('parentId')->id(false)->index();
$permissions->add('key')->string();
$permissions->add('value')->string();
$permissions->add('createdAt')->dateCreate();
$permissions->add('updatedAt')->dateUpdate();

// invoices
$invoices = Migration::table('invoices');
$invoices->add('id')->id();

// invoice lines
$invoiceLine = Migration::table('invoiceLines');
$invoiceLine->add('id')->id();

// customers
$customers = Migration::table('customers');
$customers->add('id')->id();

// addresses
$addresses = Migration::table('addresses');
$addresses->add('id')->id();

// accounts
$accounts = Migration::table('accounts');
$accounts->add('id')->id();
$accounts->add('administrationId')->id(false)->relation('administrations', 'id');
$accounts->add('desc')->string(40);
$accounts->add('code')->int(2);
$accounts->add('type')->string(40);
$accounts->add('vat')->type('TINYINT');
$accounts->add('isActive')->bool(true);
$accounts->add('createdAt')->dateCreate();
$accounts->add('updatedAt')->dateUpdate();

// bookings
$bookings = Migration::table('bookings');
$bookings->add('id')->id();
$bookings->add('administrationId')->id(false)->relation('administrations', 'id');
$bookings->add('accountId')->id(false)->relation('accounts', 'id');
$bookings->add('type')->string(255)->index();
$bookings->add('desc')->string(255)->nullable();
$bookings->add('period')->date()->index();
$bookings->add('openingBalance')->price();
$bookings->add('reference')->id(false)->index()->nullable();
$bookings->add('createdAt')->dateCreate();
$bookings->add('updatedAt')->dateUpdate();

// lines
$lines = Migration::table('lines');
$lines->add('id')->id();
$lines->add('accountId')->id(false)->relation('accounts', 'id');
$lines->add('bookingId')->id(false)->relation('bookings', 'id');
$lines->add('desc')->string(255)->nullable();
$lines->add('price')->price()->nullable(); // above 0 is credit, below is debit
$lines->add('createdAt')->dateCreate();
$lines->add('updatedAt')->dateUpdate();

// payments
$payments = Migration::table('payments');
$payments->add('id')->id();
