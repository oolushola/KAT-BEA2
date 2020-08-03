<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

//Clear config cache:
Route::get('/config-cache', function() {
	$exitCode = Artisan::call('config:cache');
	return 'Config cache cleared';
}); 

// Clear application cache:
Route::get('/clear-cache', function() {
	$exitCode = Artisan::call('cache:clear');
	return 'Application cache cleared';
});

// Clear view cache:
Route::get('/view-clear', function() {
	$exitCode = Artisan::call('view:clear');
	return 'View cache cleared';
});

Route::get('/', 'backendController@login')->name('login');
Route::post('check-login', 'backendController@checkLogin');

Route::group(['middleware' => 'auth'], function() {
    Route::get('dashboard', 'backendController@successLogin');

    Route::get('user-registration', 'backendController@userRegistration');
    Route::post('registeruser', 'backendController@registerUser');
    Route::get('user-registration/{userid}/edit', 'backendController@editUserRegistration');
    Route::patch('user-registration/{id}', 'backendController@updateUserRegistration');
    Route::get('logout', 'backendController@logout');

    Route::post('upload-profile-photo', 'dashboardController@uploadProfilePhoto');
    Route::post('change-password', 'dashboardController@changePassword');

    // Graphical Representation
    Route::get('gatedout-selected-week', 'backendController@gatedOutSelectedWeek');
    Route::get('gatedout-months-comparison', 'graphController@gatedOutMonthsCompare');
    Route::get('loading-site-monthly', 'graphController@monthlyLoadingSite');
    Route::get('loading-site-specific-day', 'graphController@loadingSiteBySpecificDay');
    Route::get('loading-site-weekly', 'graphController@loadingSiteByweekRange');
    Route::get('monthly-target-graph', 'graphController@specificMonthTarget');
    Route::get('client-trip-status-chart', 'graphController@clientTripStatus');

    Route::resource('companies-profile', 'companyProfileController');
    Route::resource('product-category', 'productTypeController');
    Route::resource('products', 'productsController');
    Route::resource('truck-types', 'trucktypeController');
    Route::resource('loading-sites', 'loadingsitesController');
    Route::resource('loading-clerk', 'loadingClerkContronller');
    Route::resource('invoice-subheading', 'invoiceSubheadingController');

    Route::resource('clients', 'clientController');
    Route::get('getstates', 'clientController@loadClientStates');
    Route::get('client-products/{clientName}/{id}', 'clientController@clientproduct');
    Route::post('client-products', 'clientController@addClientProducts');
    Route::get('client-fare-rates/{clientName}/{id}', 'clientController@clientfarerates');
    Route::post('client-fare-rates', 'clientController@storeClientRates');
    Route::get('client-fare-rates/{client_name}/{client_id}/edit/{id}', 'clientController@editclientrate');
    Route::patch('client-fare-rates/{id}', 'clientController@updateClientRates');
    Route::get('client-loading-site/{clientName}/{id}', 'clientController@clientloadingsite');
    Route::get('loadingsitesperstate', 'clientController@getloadingsitesperstate');
    Route::post('store-loading-site', 'clientController@assignLoadingSite');
    Route::post('remove-loading-site', 'clientController@removeLoadingSite');
    Route::patch('clientFareRatings', 'clientController@storeBulkClientRate');
    Route::get('client-rate/{id}/{clientName}', 'clientController@detailedSpecificClientRate');

    Route::get('trip-initial-requirement', 'ordersController@getinitialrequirement');
    Route::resource('truck-availability', 'truckAvailabilityController');
    Route::get('truck-availability-list', 'truckAvailabilityController@show');
    Route::resource('trips', 'ordersController');
    Route::get('create-trip/with/{truck_no}/{id}', 'ordersController@createTripByAvailability');
    Route::post('add-movedintruck-availability', 'ordersController@storeMovedInTruck');
    Route::post('upload-bulk-trip', 'bulkTripController@uploadBulkTrip');
    Route::post('upload-bulk-sales-order-no', 'bulkTripController@bulksalesOrder');
    Route::post('upload-bulk-drivers', 'bulkTripController@bulkdrivers');
    Route::post('upload-bulk-truckanddriverpairing', 'bulkTripController@truckDriver');
    Route::post('upload-bulk-event-time', 'bulkTripController@eventTime');

    Route::get('trip/{orderId}/{clientName}', 'ordersController@eventTrip');
    Route::get('trip/{orderId}/{clientName}/{eventid}/edit', 'ordersController@editeventTrip');
    Route::patch('trip-cancel-order/{id}', 'ordersController@voidTrip');
    Route::get('voided-trips', 'ordersController@showVoidedTrips');
    Route::get('on-journey-trips', 'ordersController@showOnlyOnJourneyTrip');

    Route::post('trip-event', 'ordersController@storeOrderEvent');
    Route::patch('trip-event/{tripeventid}', 'ordersController@updateTripEvent');
    Route::get('way-bill/{orderId}/{clientName}', 'ordersController@waybill');
    Route::post('way-bill', 'ordersController@storewaybilldetails');
    Route::get('way-bill/{orderId}/{clientName}/{id}', 'ordersController@editwaybill');
    Route::patch('way-bill/{id}', 'ordersController@updatewaybill');
    Route::post('approve-waybill/{waybill_id}', 'ordersController@approveWaybill');
    Route::post('waybill-remarks', 'ordersController@waybillRemark');
    Route::get('trip-overview/{kayaid}', 'overviewController@displaytripoverview');
    Route::post('trip-overview-payment-request', 'overviewController@requestPayment');
    Route::get('payment-request', 'overviewController@paymentRequest');
    Route::get('delete-specific-waybill', 'ordersController@deleteSpecificWaybill');

    Route::post('approve-advance-payment', 'overviewController@approveAdvancePayment');

    Route::get('update-trip', 'ordersController@fieldOpsUpdate');
    Route::get('view-orders', 'ordersController@show');
    Route::get('generate-report', 'ordersController@clientReport');
    Route::get('client-report', 'ordersController@displayClientReport');
    Route::post('completed-report', 'ordersController@markCompletedReport');

    Route::get('transporter-phone', 'ordersController@getTransporterNumber');
    Route::get('truck-info', 'ordersController@getTruckInformation');
    Route::get('driver-info', 'ordersController@getDriversInformation');
    Route::get('exact-destination', 'ordersController@getExactDestination');
    Route::get('client-loading-site', 'ordersController@clientLoadingSite');


    // Kaya finance 
    Route::resource('transporter-rate', 'transporterrateController');
    Route::post('bulk-transporter-rate', 'transporterrateController@storeBulkTransporterRate');
    Route::resource('bulk-payment', 'bulkpaymentController');
    Route::post('approvebulkpayment', 'bulkpaymentController@approvePayment');
    Route::post('initiate-payment/{paymentid}', 'overviewController@initiatePayment');
    Route::post('initiate-balance/{paymentid}', 'overviewController@initiateBalance');
    Route::post('advance-exception', 'paymentExceptionController@advanceException');
    Route::get('balance-exact-location', 'paymentExceptionController@getStates');
    Route::get('transporter-rate-amount', 'paymentExceptionController@getNewAmount');
    Route::post('balance-exception', 'paymentExceptionController@balanceException');
    Route::post('proceed-balance-initiation', 'paymentExceptionController@balanceInitiation');
    Route::post('approve-balance', 'paymentExceptionController@approveBalanceRequest');
    Route::get('invoices', 'invoiceController@invoiceArchive');
    Route::get('invoice-by-client', 'invoiceController@invoiceByClient');
    Route::post('invoice', 'invoiceController@invoiceTemplate');
    ROute::post('complete-invoicing', 'invoiceController@invoicedWaybill');
    Route::get('all-invoiced-trips', 'invoiceController@allInvoicedTrip');
    Route::get('invoice-trip/{invoiceNumber}', 'invoiceController@singleInvoice');
    Route::post('paid-invoices', 'invoiceController@paidInvoices');
    Route::get('invoice-multi-search', 'invoiceController@bulksearchinvoice');
    Route::get('multi-search', 'invoiceController@multipleinvoicesearch');
    Route::resource('local-purchase-order', 'lpoController');
    Route::get('financials/overview', 'financialController@financialsOverview');
    Route::get('financials/dashboard', 'financialController@displayFinancialRecords');
    Route::post('invoice-incentives', 'invoiceController@addIncentives');
    Route::post('store-incentives', 'invoiceController@storeIncentives');
    Route::get('extreme-waybill', 'financialController@getExtremeWaybill');
    Route::get('warning-waybill', 'financialController@getWarningWaybill');
    Route::get('healthy-waybill', 'financialController@getHealthyWaybill');
    Route::post('update-initial-invoice-price', 'invoiceController@updateTripAmount');

    Route::resource('transporters', 'transporterController');
    Route::resource('trucks', 'trucksController');
    Route::resource('drivers', 'driversController');
    Route::resource('assign-driver-truck', 'truckDriverController');
    Route::get('request-transporter-payment', 'transporterController@requestForPayment');
    Route::get('advance-request-payment', 'transporterController@advanceRequestPayment');
    Route::get('balance-request-payment', 'transporterController@balanceRequestPayment');
    Route::post('upload-collected-waybill-proof', 'transporterController@uploadCollectedWaybillProof');
//    Route::get('transporter-account-update/{id}', 'transporterController@updateTransporterAccountDetails');
    

    
    Route::resource('cargo-availability', 'cargoAvailabilityController');
    Route::resource('kaya-target', 'targetController');

    Route::get('client-trip-sort', 'sortController@clienttrips');
    Route::get('sort-loading-site-client', 'sortController@sortByloadingSiteandClient');
    Route::get('sort-tracker', 'sortController@sortByTracker');
    Route::get('sort-transporters', 'sortController@sortByTransporters');
    Route::get('sort-waybill-status', 'sortController@sortByWaybillstatus');

    // Sort and Filter By Year
    Route::get('filter-by-year', 'yearElasticSortController@filterYearOnly');
    Route::get('filter-year-month', 'yearElasticSortController@filterYearandMonth');
    Route::get('filter-year-client', 'yearElasticSortController@filterYearandClient');
    Route::get('filter-year-loading-site', 'yearElasticSortController@filterYearandLoadingsite');
    Route::get('filter-year-transporter', 'yearElasticSortController@filterYearTransporter');
    Route::get('filter-year-product', 'yearElasticSortController@filterYearProduct');
    Route::get('filter-year-state', 'yearElasticSortController@filterYearState');

    // Sort and filter by month
    Route::get('filter-month-only', 'monthElasticSortController@monthonly');
    Route::get('filter-month-client', 'monthElasticSortController@monthandclient');
    Route::get('filter-month-loading-site', 'monthElasticSortController@monthandloadingsite');
    Route::get('filter-month-transporters', 'monthElasticSortController@monthandtransporters');
    Route::get('filter-month-destination', 'monthElasticSortController@monthanddestination');
    Route::get('filter-month-products', 'monthElasticSortController@monthandproducts');
    Route::get('filter-month-client-ls', 'monthElasticSortController@monthclientloadingsite');
    Route::get('filter-month-destination-el', 'monthElasticSortController@monthdestinationexact');

    // Sort and filter by week
    Route::get('filter-by-weekonly', 'weekElasticSortController@filterweekonly');
    Route::get('filter-week-by-product', 'weekElasticSortController@filterweekproduct');
    Route::get('filter-week-by-loadingsite', 'weekElasticSortController@filterweekloadingsite');
    Route::get('filter-week-all-criteria', 'weekElasticSortController@filterweeklyfullcriteria');

    Route::resource('incentives', 'incentivesController');

    Route::patch('delete-transporters-document/{id}/', 'transporterController@deleteTransporterDocument');
    Route::post('remove-incentive/{id}', 'invoiceController@removeIncentive');
    Route::post('update-waybill-invoice', 'invoiceController@financeWaybillUpload');
    Route::post('delete-invoice/{invoiceNumber}', 'invoiceController@deleteInvoice');
    Route::resource('vat-rate', 'vatRateController');

    Route::post('invoice-biller', 'invoiceController@invoiceBiller');
    Route::post('alter-trip-information', 'invoiceController@alterTripInformation');

    Route::get('get-client-address', 'invoiceController@clientAddress');
    Route::get('cancel-acknowledgment', 'invoiceController@cancelAcknowledgement');
    Route::get('remove-payment', 'invoiceController@removePayment');

    Route::group(['middleware' => 'threadview'], function(){
        Route::get('view-trip-thread', 'ordersController@viewTripThread');
        Route::get('specific-trip-thread', 'ordersController@specificTripThread');
    
    });

    Route::get('remove-specific-trip-on-invoice', 'invoiceController@removeSpecificTripOnInvoice');
    Route::post('add-more-trip-to-invoice', 'invoiceController@addMoreTripToSpecificInvoice');
    Route::post('add-special-remark', 'invoiceController@addSpecialRemark');
    
    Route::get('offloading/my-trips-view', 'offloadingClerkController@showTripsForOffload');
    Route::get('users/assign-adhoc-staff-to-region', 'offloadingClerkController@assignaddHocStaffToRegion');
    Route::get('user-assigned-states', 'offloadingClerkController@getUserAssignedRegion');
    Route::post('assign-adhoc-clerk-region', 'offloadingClerkController@addRegionToUsers');
    Route::post('remove-adhoc-clerk-region', 'offloadingClerkController@removeRegionFromUsers');
    Route::patch('update-trip-event-offloading-clerk', 'offloadingClerkController@offloadingClerkEventUpdate');
    Route::get('offloading-clerk-notification', 'offloadingClerkController@offloadingClerkNotification');

    Route::post('update-outstanding-balance', 'paymentExceptionController@updateOutstandingBalance');

    Route::resource('super-client', 'superClientController');
    Route::get('update-account-officer-id-column', 'transporterController@updateTripAccountOfficerId');

    Route::group(['middleware' => 'bussinessUnitHead'], function() {
        Route::get('performance-metrics', 'performanceMetricController@performanceMetrics');
        Route::get('performance-metrics/{userid}/{role_id}', 'performanceMetricController@businessUnitHead');
        Route::post('update-client-rate', 'performanceMetricController@updateClientRate');
    });

    Route::resource('buh-target', 'buhTargetMonthlyController');
    
    Route::get('update-transporter-rate/{tripId}', 'transporterController@updateTrRate');
    Route::get('update-client-rate/{tripId}', 'overviewController@updateClientRate');
    Route::get('finance-update-transporter-rate/{tripId}', 'overviewController@updateTransporterRate');
    Route::get('update-balance-payment', 'overviewController@updateTransporterRateOnBalance');

    Route::get('update/invoice-incentive', 'invoiceController@updateTripIncentive');
    Route::get('update-invoice-number-and-date', 'invoiceController@updateInvoiceNumberAndDate');

    
    
    // Financial Record Filtering...
    Route::get('client-loading-site-finance', 'financialController@loadingSiteOnFinance');
    Route::get('client-loading-site-invoice-status', 'FinanceSortController@clientLoadingSiteInvoiceStatus');
    Route::get('client-invoice-status', 'FinanceSortController@clientAndInvoiceStatus');
    Route::get('finance-client-loading-site', 'FinanceSortController@financeClientLoadingSite');
    Route::get('finance-client-destination', 'FinanceSortController@clientDestination');
    Route::get('finance-invoice', 'FinanceSortController@financeInvoice');
    Route::get('finance-date-range', 'FinanceSortController@financeDateRange');
    Route::get('finance-client-date-range', 'FinanceSortController@financeClientDateRange');
    Route::get('finance-client-invoice-payment', 'FinanceSortController@financeClientInvoicePayment');
    Route::get('finance-payment-status', 'FinanceSortController@financePaymentStatus');
    Route::get('finance-client-invoice-date-range', 'FinanceSortController@financeClientInvoiceDateRange');

    //finance-client-invoice-payment

    Route::get('invoice-collage/{invoiceNumber}', 'invoiceController@invoiceCollage');
    
});


