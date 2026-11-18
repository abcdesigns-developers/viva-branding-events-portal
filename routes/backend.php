<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\BlogController;
use App\Http\Controllers\Admin\IconController;
use App\Http\Controllers\Admin\ItemController;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\JobcardController;
use App\Http\Controllers\Admin\StudentController;
use App\Http\Controllers\Admin\TeacherController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\DepartmentController;
use App\Http\Controllers\Admin\ZoneController;
use App\Http\Controllers\Admin\EventController;
use App\Http\Controllers\Admin\EventTypeController;
use App\Http\Controllers\Admin\GiftItemController;
use App\Http\Controllers\Admin\PopItemController;
use App\Http\Controllers\Admin\ProductSampleController;
use App\Http\Controllers\Admin\CatalogueItemController;
use App\Http\Controllers\Admin\SponsorshipDeliverableController;
use App\Http\Controllers\Admin\BrandingMaterialController;
use App\Http\Controllers\Admin\GuestCategoryController;
use App\Http\Controllers\Admin\HotelController;
use App\Http\Controllers\Admin\LocationController;
use App\Http\Controllers\Admin\MeetEventRequestController;
use App\Http\Controllers\Admin\ApprovalController;
use App\Http\Controllers\Admin\MeetPlanningController;
use App\Http\Controllers\Admin\PurchaseTaskController;
use App\Http\Controllers\Admin\AccountsTaskController;
use App\Http\Controllers\Admin\LogisticsTaskController;
use App\Http\Controllers\Admin\ExhibitionRequestController;
use App\Http\Controllers\Admin\PresentationRequestController;
use App\Http\Controllers\Admin\GiftPopRequestController;
use App\Http\Controllers\Admin\BrandingRequestController;
use App\Http\Controllers\Admin\ExperienceCentreRequestController;
use App\Http\Controllers\RazorpayPaymentController;
use App\Http\Controllers\Admin\ItemcategoryController;

//End of use statements

Route::middleware(['auth', 'admin', 'preventBackHistory'])->group(function () {
    Route::prefix('admin')->name('admin.')->group(function () {
        

        Route::get('dashboard', [DashboardController::class, 'index'])->name('dashboard.index');

        //Roles
        Route::resource('roles', RoleController::class);
        Route::post('roles/data', [RoleController::class, 'data'])->name('roles.data');
        Route::post('roles/list',[RoleController::class, 'list'])->name('roles.list');

        //Permissions
        Route::get('roles/{role}/permission/show',[RoleController::class, 'permissionsShow'])->name('roles.permissions.show');
        Route::post('roles/{role}/permission/update',[RoleController::class, 'permissionsUpdate'])->name('roles.permissions.update');

        //Departments
        Route::resource('departments', DepartmentController::class);
        Route::post('departments/data', [DepartmentController::class, 'data'])->name('departments.data');
        Route::post('departments/list', [DepartmentController::class, 'list'])->name('departments.list');
        Route::post('departments/change-status', [DepartmentController::class, 'changeStatus'])->name('departments.change.status');


        //Master Management
        //Zones
        Route::resource('zones', ZoneController::class);
        Route::post('zones/data', [ZoneController::class, 'data'])->name('zones.data');
        Route::post('zones/list', [ZoneController::class, 'list'])->name('zones.list');
        Route::post('zones/change-status', [ZoneController::class, 'changeStatus'])->name('zones.change.status');

        //Events
        Route::resource('events', EventController::class);
        Route::post('events/data', [EventController::class, 'data'])->name('events.data');
        Route::post('events/list', [EventController::class, 'list'])->name('events.list');
        Route::post('events/change-status', [EventController::class, 'changeStatus'])->name('events.change.status');

        //Event Types
        Route::resource('event-types', EventTypeController::class);
        Route::post('event-types/data', [EventTypeController::class, 'data'])->name('event-types.data');
        Route::post('event-types/list', [EventTypeController::class, 'list'])->name('event-types.list');
        Route::post('event-types/change-status', [EventTypeController::class, 'changeStatus'])->name('event-types.change.status');

        //Gift Items
        Route::resource('gift-items', GiftItemController::class);
        Route::post('gift-items/data', [GiftItemController::class, 'data'])->name('gift-items.data');
        Route::post('gift-items/list', [GiftItemController::class, 'list'])->name('gift-items.list');
        Route::post('gift-items/change-status', [GiftItemController::class, 'changeStatus'])->name('gift-items.change.status');

        //POP Items
        Route::resource('pop-items', PopItemController::class);
        Route::post('pop-items/data', [PopItemController::class, 'data'])->name('pop-items.data');
        Route::post('pop-items/list', [PopItemController::class, 'list'])->name('pop-items.list');
        Route::post('pop-items/change-status', [PopItemController::class, 'changeStatus'])->name('pop-items.change.status');

        //Product Samples
        Route::resource('product-samples', ProductSampleController::class);
        Route::post('product-samples/data', [ProductSampleController::class, 'data'])->name('product-samples.data');
        Route::post('product-samples/list', [ProductSampleController::class, 'list'])->name('product-samples.list');
        Route::post('product-samples/change-status', [ProductSampleController::class, 'changeStatus'])->name('product-samples.change.status');

        //Catalogue Items
        Route::resource('catalogue-items', CatalogueItemController::class);
        Route::post('catalogue-items/data', [CatalogueItemController::class, 'data'])->name('catalogue-items.data');
        Route::post('catalogue-items/list', [CatalogueItemController::class, 'list'])->name('catalogue-items.list');
        Route::post('catalogue-items/change-status', [CatalogueItemController::class, 'changeStatus'])->name('catalogue-items.change.status');

        //Sponsorship Deliverables
        Route::resource('sponsorship-deliverables', SponsorshipDeliverableController::class);
        Route::post('sponsorship-deliverables/data', [SponsorshipDeliverableController::class, 'data'])->name('sponsorship-deliverables.data');
        Route::post('sponsorship-deliverables/list', [SponsorshipDeliverableController::class, 'list'])->name('sponsorship-deliverables.list');
        Route::post('sponsorship-deliverables/change-status', [SponsorshipDeliverableController::class, 'changeStatus'])->name('sponsorship-deliverables.change.status');

        //Branding Materials
        Route::resource('branding-materials', BrandingMaterialController::class);
        Route::post('branding-materials/data', [BrandingMaterialController::class, 'data'])->name('branding-materials.data');
        Route::post('branding-materials/list', [BrandingMaterialController::class, 'list'])->name('branding-materials.list');
        Route::post('branding-materials/change-status', [BrandingMaterialController::class, 'changeStatus'])->name('branding-materials.change.status');

        //Guest Categories
        Route::resource('guest-categories', GuestCategoryController::class);
        Route::post('guest-categories/data', [GuestCategoryController::class, 'data'])->name('guest-categories.data');
        Route::post('guest-categories/list', [GuestCategoryController::class, 'list'])->name('guest-categories.list');
        Route::post('guest-categories/change-status', [GuestCategoryController::class, 'changeStatus'])->name('guest-categories.change.status');

        //Hotels
        Route::resource('hotels', HotelController::class);
        Route::post('hotels/data', [HotelController::class, 'data'])->name('hotels.data');
        Route::post('hotels/list', [HotelController::class, 'list'])->name('hotels.list');
        Route::post('hotels/change-status', [HotelController::class, 'changeStatus'])->name('hotels.change.status');

        //Locations
        Route::resource('locations', LocationController::class);
        Route::post('locations/data', [LocationController::class, 'data'])->name('locations.data');
        Route::post('locations/list', [LocationController::class, 'list'])->name('locations.list');
        Route::post('locations/change-status', [LocationController::class, 'changeStatus'])->name('locations.change.status');

        //Requests – Meet Events ({portal_request} is the Hashids key of a PortalRequest)
        Route::resource('meet-event-requests', MeetEventRequestController::class)->parameters(['meet-event-requests' => 'portal_request'])->except(['destroy']);
        Route::post('meet-event-requests/data', [MeetEventRequestController::class, 'data'])->name('meet-event-requests.data');
        Route::get('meet-event-requests/{portal_request}/documents/{request_document}', [MeetEventRequestController::class, 'document'])->name('meet-event-requests.document');

        //Stock ledger (Gift, POP, Catalogue, Sample)
        Route::get('stock', [\App\Http\Controllers\Admin\StockController::class, 'index'])->name('stock.index');
        Route::post('stock', [\App\Http\Controllers\Admin\StockController::class, 'store'])->name('stock.store');
        Route::get('stock/{type}/{id}', [\App\Http\Controllers\Admin\StockController::class, 'show'])->name('stock.show')->whereNumber('id');

        //Meet Events – work done on the request page ({portal_request} / {team_task} are Hashids keys)
        //Branding: plan, event ready, completed, closed
        Route::post('meet-plannings/{portal_request}', [MeetPlanningController::class, 'store'])->name('meet-plannings.store');
        Route::put('meet-plannings/{portal_request}', [MeetPlanningController::class, 'update'])->name('meet-plannings.update');
        Route::post('meet-plannings/{portal_request}/event-ready', [MeetPlanningController::class, 'eventReady'])->name('meet-plannings.event-ready');
        Route::post('meet-plannings/{portal_request}/complete', [MeetPlanningController::class, 'complete'])->name('meet-plannings.complete');
        Route::post('meet-plannings/{portal_request}/close', [MeetPlanningController::class, 'close'])->name('meet-plannings.close');

        //Purchase – vendor and PO
        Route::post('purchase-tasks/{team_task}/purchase-orders', [PurchaseTaskController::class, 'store'])->name('purchase-tasks.store');
        Route::post('purchase-tasks/{team_task}/complete', [PurchaseTaskController::class, 'complete'])->name('purchase-tasks.complete');

        //Accounts – advance and payment
        Route::post('accounts-tasks/{team_task}/payments', [AccountsTaskController::class, 'store'])->name('accounts-tasks.store');
        Route::post('accounts-tasks/{team_task}/complete', [AccountsTaskController::class, 'complete'])->name('accounts-tasks.complete');

        //Logistics – material and dispatch
        Route::post('logistics-tasks/{team_task}/items', [LogisticsTaskController::class, 'items'])->name('logistics-tasks.items');
        Route::post('logistics-tasks/{team_task}/dispatch', [LogisticsTaskController::class, 'sendOut'])->name('logistics-tasks.dispatch');

        //Director / Vipin Sir – approve or return
        Route::post('approvals/{portal_request}/approve', [ApprovalController::class, 'approve'])->name('approvals.approve');
        Route::post('approvals/{portal_request}/return', [ApprovalController::class, 'sendBack'])->name('approvals.return');

        //Requests – Events / Exhibitions / Sponsorship
        // Phase 2 module: 404 until PORTAL_PHASE2=true
        Route::middleware('phase2')->group(function () {
            Route::resource('exhibition-requests', ExhibitionRequestController::class)->parameters(['exhibition-requests' => 'portal_request'])->except(['destroy']);
            Route::post('exhibition-requests/data', [ExhibitionRequestController::class, 'data'])->name('exhibition-requests.data');
            Route::get('exhibition-requests/{portal_request}/documents/{request_document}', [ExhibitionRequestController::class, 'document'])->name('exhibition-requests.document');
        });

        //Requests – Presentations & Small Events
        Route::resource('presentation-requests', PresentationRequestController::class)->parameters(['presentation-requests' => 'portal_request'])->except(['destroy']);
        Route::post('presentation-requests/data', [PresentationRequestController::class, 'data'])->name('presentation-requests.data');
        Route::get('presentation-requests/{portal_request}/documents/{request_document}', [PresentationRequestController::class, 'document'])->name('presentation-requests.document');

        //Requests – Gift & POP Requirement
        Route::resource('gift-pop-requests', GiftPopRequestController::class)->parameters(['gift-pop-requests' => 'portal_request'])->except(['destroy']);
        Route::post('gift-pop-requests/data', [GiftPopRequestController::class, 'data'])->name('gift-pop-requests.data');

        //Requests – Branding Support & Trade Marketing
        // Phase 2 module: 404 until PORTAL_PHASE2=true
        Route::middleware('phase2')->group(function () {
            Route::resource('branding-requests', BrandingRequestController::class)->parameters(['branding-requests' => 'portal_request'])->except(['destroy']);
            Route::post('branding-requests/data', [BrandingRequestController::class, 'data'])->name('branding-requests.data');
            Route::get('branding-requests/{portal_request}/documents/{request_document}', [BrandingRequestController::class, 'document'])->name('branding-requests.document');
        });

        //Requests – New Experience Centre
        // Phase 2 module: 404 until PORTAL_PHASE2=true
        Route::middleware('phase2')->group(function () {
            Route::resource('experience-centre-requests', ExperienceCentreRequestController::class)->parameters(['experience-centre-requests' => 'portal_request'])->except(['destroy']);
            Route::post('experience-centre-requests/data', [ExperienceCentreRequestController::class, 'data'])->name('experience-centre-requests.data');
            Route::get('experience-centre-requests/{portal_request}/documents/{request_document}', [ExperienceCentreRequestController::class, 'document'])->name('experience-centre-requests.document');
        });

        //End of File
    });
});