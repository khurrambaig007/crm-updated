<?php

use App\Http\Controllers\AgentController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\PasswordResetController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\CarrierController;
use App\Http\Controllers\ChargeController;
use App\Http\Controllers\CommodityController;
use App\Http\Controllers\ContainerKindController;
use App\Http\Controllers\ContainerSizeController;
use App\Http\Controllers\ContainerTypeController;
use App\Http\Controllers\CurrencyExchangeRateController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\InvestorController;
use App\Http\Controllers\MaintenanceRepairEntryController;
use App\Http\Controllers\PartyController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\PurchaseInvoiceController;
use App\Http\Controllers\PolController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\SettlementTypeController;
use App\Http\Controllers\ShipperBpController;
use App\Http\Controllers\SlotTermController;
use App\Http\Controllers\SubCompanyController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\SystemOperationsController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\VesselVoyageController;
use Illuminate\Support\Facades\Route;

Route::get('/', fn () => auth()->check() ? redirect()->route('dashboard') : redirect()->route('login'));

Route::middleware('guest')->group(function () {
    Route::get('login', [LoginController::class, 'create'])->name('login');
    Route::post('login', [LoginController::class, 'store'])->name('login.store')->middleware('throttle:5,1');
    Route::get('register', [RegisterController::class, 'create'])->name('register');
    Route::post('register', [RegisterController::class, 'store'])->name('register.store');

    Route::get('forgot-password', [PasswordResetController::class, 'create'])->name('password.request');
    Route::post('forgot-password', [PasswordResetController::class, 'store'])->name('password.email');
    Route::get('reset-password/{token}', [PasswordResetController::class, 'edit'])->name('password.reset');
    Route::post('reset-password', [PasswordResetController::class, 'update'])->name('password.store');
});

Route::middleware('auth')->group(function () {
    Route::post('logout', [LoginController::class, 'destroy'])->name('logout');

    Route::middleware('permission:dashboard.view')->group(function () {
        Route::get('dashboard', DashboardController::class)->name('dashboard');
    });

    Route::middleware('permission:maintenance_repair_entries.view')->group(function () {
        Route::get('maintenance-repair-entries', [MaintenanceRepairEntryController::class, 'index'])->name('maintenance-repair-entries.index');
    });
    Route::middleware('permission:maintenance_repair_entries.add')->group(function () {
        Route::get('maintenance-repair-entries/create', [MaintenanceRepairEntryController::class, 'create'])->name('maintenance-repair-entries.create');
        Route::post('maintenance-repair-entries', [MaintenanceRepairEntryController::class, 'store'])->name('maintenance-repair-entries.store');
    });
    Route::middleware('permission:maintenance_repair_entries.edit')->group(function () {
        Route::get('maintenance-repair-entries/{maintenanceRepairEntry}/edit', [MaintenanceRepairEntryController::class, 'edit'])->name('maintenance-repair-entries.edit');
        Route::patch('maintenance-repair-entries/{maintenanceRepairEntry}', [MaintenanceRepairEntryController::class, 'update'])->name('maintenance-repair-entries.update');
    });
    Route::middleware('permission:maintenance_repair_entries.delete')->delete('maintenance-repair-entries/{maintenanceRepairEntry}', [MaintenanceRepairEntryController::class, 'destroy'])->name('maintenance-repair-entries.destroy');

    Route::middleware('permission:purchase_invoices.view')->get('purchase-invoices', [PurchaseInvoiceController::class, 'index'])->name('purchase-invoices.index');
    Route::middleware('permission:purchase_invoices.add')->group(function () {
        Route::get('purchase-invoices/create', [PurchaseInvoiceController::class, 'create'])->name('purchase-invoices.create');
        Route::post('purchase-invoices', [PurchaseInvoiceController::class, 'store'])->name('purchase-invoices.store');
    });
    Route::middleware('permission:purchase_invoices.edit')->group(function () {
        Route::get('purchase-invoices/{purchaseInvoice}/edit', [PurchaseInvoiceController::class, 'edit'])->name('purchase-invoices.edit');
        Route::patch('purchase-invoices/{purchaseInvoice}', [PurchaseInvoiceController::class, 'update'])->name('purchase-invoices.update');
        Route::get('purchase-invoices/{purchaseInvoice}/navigate', [PurchaseInvoiceController::class, 'navigate'])->name('purchase-invoices.navigate');
        Route::post('purchase-invoices/{purchaseInvoice}/details', [PurchaseInvoiceController::class, 'storeDetail'])->name('purchase-invoices.details.store');
        Route::patch('purchase-invoices/{purchaseInvoice}/details/{detail}', [PurchaseInvoiceController::class, 'updateDetail'])->name('purchase-invoices.details.update');
        Route::delete('purchase-invoices/{purchaseInvoice}/details/{detail}', [PurchaseInvoiceController::class, 'destroyDetail'])->name('purchase-invoices.details.destroy');
    });
    Route::middleware('permission:purchase_invoices.delete')->delete('purchase-invoices/{purchaseInvoice}', [PurchaseInvoiceController::class, 'destroy'])->name('purchase-invoices.destroy');

    Route::middleware('permission:system_operations.view')->get('system-operations', SystemOperationsController::class)->name('system-operations.index');

    Route::prefix('system-operations')->group(function () {
        Route::middleware('permission:container_sizes.view')->group(function () {
            Route::get('container-sizes', [ContainerSizeController::class, 'index'])->name('container-sizes.index');
        });
        Route::middleware('permission:container_sizes.add')->group(function () {
            Route::get('container-sizes/create', [ContainerSizeController::class, 'create'])->name('container-sizes.create');
            Route::post('container-sizes', [ContainerSizeController::class, 'store'])->name('container-sizes.store');
        });
        Route::middleware('permission:container_sizes.edit')->group(function () {
            Route::get('container-sizes/{containerSize}/edit', [ContainerSizeController::class, 'edit'])->name('container-sizes.edit');
            Route::patch('container-sizes/{containerSize}', [ContainerSizeController::class, 'update'])->name('container-sizes.update');
        });
        Route::middleware('permission:container_sizes.delete')->delete('container-sizes/{containerSize}', [ContainerSizeController::class, 'destroy'])->name('container-sizes.destroy');

        Route::middleware('permission:port_locations.view')->group(function () {
            Route::get('port-locations', [PolController::class, 'index'])->name('port-locations.index');
        });
        Route::middleware('permission:port_locations.add')->group(function () {
            Route::get('port-locations/create', [PolController::class, 'create'])->name('port-locations.create');
            Route::post('port-locations', [PolController::class, 'store'])->name('port-locations.store');
        });
        Route::middleware('permission:port_locations.edit')->group(function () {
            Route::get('port-locations/{pol}/edit', [PolController::class, 'edit'])->name('port-locations.edit');
            Route::patch('port-locations/{pol}', [PolController::class, 'update'])->name('port-locations.update');
        });
        Route::middleware('permission:port_locations.delete')->delete('port-locations/{pol}', [PolController::class, 'destroy'])->name('port-locations.destroy');

        Route::middleware('permission:carriers.view')->group(function () {
            Route::get('carriers', [CarrierController::class, 'index'])->name('carriers.index');
        });
        Route::middleware('permission:carriers.add')->group(function () {
            Route::get('carriers/create', [CarrierController::class, 'create'])->name('carriers.create');
            Route::post('carriers', [CarrierController::class, 'store'])->name('carriers.store');
        });
        Route::middleware('permission:carriers.edit')->group(function () {
            Route::get('carriers/{carrier}/edit', [CarrierController::class, 'edit'])->name('carriers.edit');
            Route::patch('carriers/{carrier}', [CarrierController::class, 'update'])->name('carriers.update');
        });
        Route::middleware('permission:carriers.delete')->delete('carriers/{carrier}', [CarrierController::class, 'destroy'])->name('carriers.destroy');

        Route::middleware('permission:agents.view')->group(function () {
            Route::get('agents', [AgentController::class, 'index'])->name('agents.index');
        });
        Route::middleware('permission:agents.add')->group(function () {
            Route::get('agents/create', [AgentController::class, 'create'])->name('agents.create');
            Route::post('agents', [AgentController::class, 'store'])->name('agents.store');
        });
        Route::middleware('permission:agents.edit')->group(function () {
            Route::get('agents/{agent}/edit', [AgentController::class, 'edit'])->name('agents.edit');
            Route::patch('agents/{agent}', [AgentController::class, 'update'])->name('agents.update');
        });
        Route::middleware('permission:agents.delete')->delete('agents/{agent}', [AgentController::class, 'destroy'])->name('agents.destroy');

        Route::middleware('permission:container_types.view')->group(function () {
            Route::get('container-types', [ContainerTypeController::class, 'index'])->name('container-types.index');
        });
        Route::middleware('permission:container_types.add')->group(function () {
            Route::get('container-types/create', [ContainerTypeController::class, 'create'])->name('container-types.create');
            Route::post('container-types', [ContainerTypeController::class, 'store'])->name('container-types.store');
        });
        Route::middleware('permission:container_types.edit')->group(function () {
            Route::get('container-types/{containerType}/edit', [ContainerTypeController::class, 'edit'])->name('container-types.edit');
            Route::patch('container-types/{containerType}', [ContainerTypeController::class, 'update'])->name('container-types.update');
        });
        Route::middleware('permission:container_types.delete')->delete('container-types/{containerType}', [ContainerTypeController::class, 'destroy'])->name('container-types.destroy');

        Route::middleware('permission:slots.view')->group(function () {
            Route::get('slots', [SlotTermController::class, 'index'])->name('slots.index');
        });
        Route::middleware('permission:slots.add')->group(function () {
            Route::get('slots/create', [SlotTermController::class, 'create'])->name('slots.create');
            Route::post('slots', [SlotTermController::class, 'store'])->name('slots.store');
        });
        Route::middleware('permission:slots.edit')->group(function () {
            Route::get('slots/{slotTerm}/edit', [SlotTermController::class, 'edit'])->name('slots.edit');
            Route::patch('slots/{slotTerm}', [SlotTermController::class, 'update'])->name('slots.update');
        });
        Route::middleware('permission:slots.delete')->delete('slots/{slotTerm}', [SlotTermController::class, 'destroy'])->name('slots.destroy');

        Route::middleware('permission:investors.view')->group(function () {
            Route::get('investors', [InvestorController::class, 'index'])->name('investors.index');
        });
        Route::middleware('permission:investors.add')->group(function () {
            Route::get('investors/create', [InvestorController::class, 'create'])->name('investors.create');
            Route::post('investors', [InvestorController::class, 'store'])->name('investors.store');
        });
        Route::middleware('permission:investors.edit')->group(function () {
            Route::get('investors/{investor}/edit', [InvestorController::class, 'edit'])->name('investors.edit');
            Route::patch('investors/{investor}', [InvestorController::class, 'update'])->name('investors.update');
        });
        Route::middleware('permission:investors.delete')->delete('investors/{investor}', [InvestorController::class, 'destroy'])->name('investors.destroy');

        Route::middleware('permission:container_kinds.view')->group(function () {
            Route::get('container-kinds', [ContainerKindController::class, 'index'])->name('container-kinds.index');
        });
        Route::middleware('permission:container_kinds.add')->group(function () {
            Route::get('container-kinds/create', [ContainerKindController::class, 'create'])->name('container-kinds.create');
            Route::post('container-kinds', [ContainerKindController::class, 'store'])->name('container-kinds.store');
        });
        Route::middleware('permission:container_kinds.edit')->group(function () {
            Route::get('container-kinds/{containerKind}/edit', [ContainerKindController::class, 'edit'])->name('container-kinds.edit');
            Route::patch('container-kinds/{containerKind}', [ContainerKindController::class, 'update'])->name('container-kinds.update');
        });
        Route::middleware('permission:container_kinds.delete')->delete('container-kinds/{containerKind}', [ContainerKindController::class, 'destroy'])->name('container-kinds.destroy');

        Route::middleware('permission:commodities.view')->group(function () {
            Route::get('commodities', [CommodityController::class, 'index'])->name('commodities.index');
        });
        Route::middleware('permission:commodities.add')->group(function () {
            Route::get('commodities/create', [CommodityController::class, 'create'])->name('commodities.create');
            Route::post('commodities', [CommodityController::class, 'store'])->name('commodities.store');
        });
        Route::middleware('permission:commodities.edit')->group(function () {
            Route::get('commodities/{commodity}/edit', [CommodityController::class, 'edit'])->name('commodities.edit');
            Route::patch('commodities/{commodity}', [CommodityController::class, 'update'])->name('commodities.update');
        });
        Route::middleware('permission:commodities.delete')->delete('commodities/{commodity}', [CommodityController::class, 'destroy'])->name('commodities.destroy');

        Route::middleware('permission:vessel_voyages.view')->group(function () {
            Route::get('vessel-voyages', [VesselVoyageController::class, 'index'])->name('vessel-voyages.index');
        });
        Route::middleware('permission:vessel_voyages.add')->group(function () {
            Route::get('vessel-voyages/create', [VesselVoyageController::class, 'create'])->name('vessel-voyages.create');
            Route::post('vessel-voyages', [VesselVoyageController::class, 'store'])->name('vessel-voyages.store');
        });
        Route::middleware('permission:vessel_voyages.edit')->group(function () {
            Route::get('vessel-voyages/{vesselVoyage}/edit', [VesselVoyageController::class, 'edit'])->name('vessel-voyages.edit');
            Route::patch('vessel-voyages/{vesselVoyage}', [VesselVoyageController::class, 'update'])->name('vessel-voyages.update');
        });
        Route::middleware('permission:vessel_voyages.delete')->delete('vessel-voyages/{vesselVoyage}', [VesselVoyageController::class, 'destroy'])->name('vessel-voyages.destroy');

        Route::middleware('permission:charges.view')->group(function () {
            Route::get('charges', [ChargeController::class, 'index'])->name('charges.index');
        });
        Route::middleware('permission:charges.add')->group(function () {
            Route::get('charges/create', [ChargeController::class, 'create'])->name('charges.create');
            Route::post('charges', [ChargeController::class, 'store'])->name('charges.store');
        });
        Route::middleware('permission:charges.edit')->group(function () {
            Route::get('charges/{charge}/edit', [ChargeController::class, 'edit'])->name('charges.edit');
            Route::patch('charges/{charge}', [ChargeController::class, 'update'])->name('charges.update');
        });
        Route::middleware('permission:charges.delete')->delete('charges/{charge}', [ChargeController::class, 'destroy'])->name('charges.destroy');

        Route::middleware('permission:currencies.view')->group(function () {
            Route::get('currency-exchange-rates', [CurrencyExchangeRateController::class, 'index'])->name('currency-exchange-rates.index');
        });
        Route::middleware('permission:currencies.add')->post('currency-exchange-rates/fetch', [CurrencyExchangeRateController::class, 'fetch'])->name('currency-exchange-rates.fetch');
        Route::middleware('permission:currencies.edit')->group(function () {
            Route::get('currency-exchange-rates/{currency}/edit', [CurrencyExchangeRateController::class, 'edit'])->name('currency-exchange-rates.edit');
            Route::patch('currency-exchange-rates/{currency}', [CurrencyExchangeRateController::class, 'update'])->name('currency-exchange-rates.update');
        });

        Route::middleware('permission:settlement_types.view')->group(function () {
            Route::get('settlement-types', [SettlementTypeController::class, 'index'])->name('settlement-types.index');
        });
        Route::middleware('permission:settlement_types.add')->group(function () {
            Route::get('settlement-types/create', [SettlementTypeController::class, 'create'])->name('settlement-types.create');
            Route::post('settlement-types', [SettlementTypeController::class, 'store'])->name('settlement-types.store');
        });
        Route::middleware('permission:settlement_types.edit')->group(function () {
            Route::get('settlement-types/{settlementType}/edit', [SettlementTypeController::class, 'edit'])->name('settlement-types.edit');
            Route::patch('settlement-types/{settlementType}', [SettlementTypeController::class, 'update'])->name('settlement-types.update');
        });
        Route::middleware('permission:settlement_types.delete')->delete('settlement-types/{settlementType}', [SettlementTypeController::class, 'destroy'])->name('settlement-types.destroy');

        Route::middleware('permission:shipper_bps.view')->group(function () {
            Route::get('shipper-bps', [ShipperBpController::class, 'index'])->name('shipper-bps.index');
        });
        Route::middleware('permission:shipper_bps.add')->group(function () {
            Route::get('shipper-bps/create', [ShipperBpController::class, 'create'])->name('shipper-bps.create');
            Route::post('shipper-bps', [ShipperBpController::class, 'store'])->name('shipper-bps.store');
        });
        Route::middleware('permission:shipper_bps.edit')->group(function () {
            Route::get('shipper-bps/{shipperBp}/edit', [ShipperBpController::class, 'edit'])->name('shipper-bps.edit');
            Route::patch('shipper-bps/{shipperBp}', [ShipperBpController::class, 'update'])->name('shipper-bps.update');
        });
        Route::middleware('permission:shipper_bps.delete')->delete('shipper-bps/{shipperBp}', [ShipperBpController::class, 'destroy'])->name('shipper-bps.destroy');

        Route::middleware('permission:suppliers.view')->group(function () {
            Route::get('suppliers', [SupplierController::class, 'index'])->name('suppliers.index');
        });
        Route::middleware('permission:suppliers.add')->group(function () {
            Route::get('suppliers/create', [SupplierController::class, 'create'])->name('suppliers.create');
            Route::post('suppliers', [SupplierController::class, 'store'])->name('suppliers.store');
        });
        Route::middleware('permission:suppliers.edit')->group(function () {
            Route::get('suppliers/{supplier}/edit', [SupplierController::class, 'edit'])->name('suppliers.edit');
            Route::patch('suppliers/{supplier}', [SupplierController::class, 'update'])->name('suppliers.update');
        });
        Route::middleware('permission:suppliers.delete')->delete('suppliers/{supplier}', [SupplierController::class, 'destroy'])->name('suppliers.destroy');

        Route::middleware('permission:sub_companies.view')->group(function () {
            Route::get('sub-companies', [SubCompanyController::class, 'index'])->name('sub-companies.index');
        });
        Route::middleware('permission:sub_companies.add')->group(function () {
            Route::get('sub-companies/create', [SubCompanyController::class, 'create'])->name('sub-companies.create');
            Route::post('sub-companies', [SubCompanyController::class, 'store'])->name('sub-companies.store');
        });
        Route::middleware('permission:sub_companies.edit')->group(function () {
            Route::get('sub-companies/{subCompany}/edit', [SubCompanyController::class, 'edit'])->name('sub-companies.edit');
            Route::patch('sub-companies/{subCompany}', [SubCompanyController::class, 'update'])->name('sub-companies.update');
        });
        Route::middleware('permission:sub_companies.delete')->delete('sub-companies/{subCompany}', [SubCompanyController::class, 'destroy'])->name('sub-companies.destroy');

        Route::middleware('permission:parties.view')->group(function () {
            Route::get('pa-parties', [PartyController::class, 'index'])->name('parties.index');
        });
        Route::middleware('permission:parties.add')->group(function () {
            Route::get('pa-parties/create', [PartyController::class, 'create'])->name('parties.create');
            Route::post('pa-parties', [PartyController::class, 'store'])->name('parties.store');
        });
        Route::middleware('permission:parties.edit')->group(function () {
            Route::get('pa-parties/{party}/edit', [PartyController::class, 'edit'])->name('parties.edit');
            Route::patch('pa-parties/{party}', [PartyController::class, 'update'])->name('parties.update');
        });
        Route::middleware('permission:parties.delete')->delete('pa-parties/{party}', [PartyController::class, 'destroy'])->name('parties.destroy');
    });

    Route::middleware('permission:profile.view')->group(function () {
        Route::get('profile', [ProfileController::class, 'edit'])->name('profile.edit');
        Route::patch('profile', [ProfileController::class, 'update'])->name('profile.update');
        Route::patch('profile/password', [ProfileController::class, 'updatePassword'])->name('profile.password');
    });

    Route::patch('theme', [ProfileController::class, 'updateTheme'])->name('theme.update');

    Route::middleware('permission:users.view')->group(function () {
        Route::get('users', [UserController::class, 'index'])->name('users.index');
    });
    Route::middleware('permission:users.add')->group(function () {
        Route::get('users/create', [UserController::class, 'create'])->name('users.create');
        Route::post('users', [UserController::class, 'store'])->name('users.store');
    });
    Route::middleware('permission:users.edit')->group(function () {
        Route::get('users/{user}/edit', [UserController::class, 'edit'])->name('users.edit');
        Route::patch('users/{user}', [UserController::class, 'update'])->name('users.update');
    });
    Route::middleware('permission:users.delete')->delete('users/{user}', [UserController::class, 'destroy'])->name('users.destroy');

    Route::middleware('permission:roles.view')->group(function () {
        Route::get('roles', [RoleController::class, 'index'])->name('roles.index');
    });
    Route::middleware('permission:roles.add')->group(function () {
        Route::get('roles/create', [RoleController::class, 'create'])->name('roles.create');
        Route::post('roles', [RoleController::class, 'store'])->name('roles.store');
    });
    Route::middleware('permission:roles.edit')->group(function () {
        Route::get('roles/{role}/edit', [RoleController::class, 'edit'])->name('roles.edit');
        Route::patch('roles/{role}', [RoleController::class, 'update'])->name('roles.update');
    });
    Route::middleware('permission:roles.delete')->delete('roles/{role}', [RoleController::class, 'destroy'])->name('roles.destroy');

    Route::middleware('permission:permissions.view')->group(function () {
        Route::get('permissions', [PermissionController::class, 'index'])->name('permissions.index');
    });
    Route::middleware('permission:permissions.edit')->patch('permissions/{role}', [PermissionController::class, 'update'])->name('permissions.update');

    Route::middleware('permission:reports.view')->get('reports', fn () => view('reports.index'))->name('reports.index');
    Route::middleware('permission:settings.view')->get('settings', fn () => view('settings.index'))->name('settings.index');
});
