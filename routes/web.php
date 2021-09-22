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
    Route::post('client-expected-margin', 'clientController@expectedMonthlyMargin');

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
    Route::get('invoice-multi-search', 'invoiceController@bulksearchinvoice');
    Route::get('multi-search', 'invoiceController@multipleinvoicesearch');
    Route::resource('local-purchase-order', 'lpoController');
    Route::get('filter-lpo', 'lpoController@filterLpo');

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
    Route::get('transporter-account-update/{id}', 'transporterController@updateTransporterAccountDetails');
    Route::get('payment-request-master', 'transporterController@masterPaymentRequest');
 
    Route::resource('cargo-availability', 'cargoAvailabilityController');
    Route::resource('kaya-target', 'targetController');

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
        Route::get('filter-performance-metrics', 'performanceMetricController@filterPerformanceMetrics');
        Route::get('specific-buh-performance', 'performanceMetricController@specificBuhPerformance');
        Route::get('performance-metrics/{userid}/{role_id}', 'performanceMetricController@businessUnitHead');
        Route::post('update-client-rate', 'performanceMetricController@updateClientRate');
        Route::post('update-transporter-rate', 'performanceMetricController@updateTransporterRate');
        Route::post('performancemetric-truckno-update', 'performanceMetricController@truckNoUpdate');
        Route::post('update-exact-location', 'performanceMetricController@exactLocationUpdate');
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
    
    Route::get('invoice-collage/{invoiceNumber}', 'invoiceController@invoiceCollage');
    Route::get('financials/receivables-tracker', 'trackerController@receivables');
    Route::resource('other-expenses', 'expensesController');

    Route::get('/client-revenue', 'trackerController@clientRevenue');
    Route::get('/client-margin-expense-gain', 'trackerController@clientMarginExpenseGain');
    
    Route::get('transporter-log', 'transporterController@transporterLog');
    Route::get('toggle-transporter-status', 'transporterController@transporterStatus');
    Route::get('{transporter}/trip/log/{transporterId}', 'transporterController@transporterTripLog');
    Route::resource('issue-types', 'IssueTypeController');
    
    Route::post('bulk-full-payment', 'paymentExceptionController@bulkPayment');
    Route::post('update-selected-full-payment', 'paymentExceptionController@updateBulkFullPayment');
    Route::get('payment-top-up', 'paymentExceptionController@paymentTopUp');
    Route::get('/update-advance-top-up/{id}', 'paymentExceptionController@advanceTopUp');
    Route::post('update-selected-zero-payment', 'paymentExceptionController@updateMultipleZeroAdvance');

    // Graphical Representation
    Route::get('/current-month-trip-details', 'graphController@tripsForTheMonth');
    Route::get('gatedout-selected-week', 'graphController@chartDateRange');
    
    Route::get('gatedout-months-comparison', 'graphController@gatedOutMonthsCompare');
    Route::get('loading-site-monthly', 'graphController@monthlyLoadingSite');
    Route::get('loading-site-specific-day', 'graphController@loadingSiteBySpecificDay');
    Route::get('loading-site-weekly', 'graphController@loadingSiteByweekRange');
    Route::get('monthly-target-graph', 'graphController@specificMonthTarget');
    Route::get('client-trip-status-chart', 'graphController@clientTripStatus');
    
    Route::get('event-log', 'ordersController@eventLog');
    Route::get('invoice-preview', 'invoiceController@invoicePreview');
    Route::get('update-amount-paid', 'invoiceController@updateAmountPaid');

    Route::get('paid-invoices', 'invoiceController@paidInvoices');
    Route::get('daily-gate-out-record', 'backendController@dailyGateOutRecord');
    Route::get('yet-to-receive-waybills', 'invoiceController@yetToReceiveWaybill');
    Route::post('receive-waybills-bulk', 'invoiceController@receiveWaybillsBulk');

    //Human Resource Management
    Route::get('/hr/dashboard', 'hrController@dashboard');
    Route::get('{user}/bio-data', 'UserController@biodata');
    Route::post('/store-bio-data', 'UserController@storeBiodata');
    Route::post('/store-user-education', 'UserController@storeUserEducation');
    Route::post('/store-user-experience', 'UserController@storeUserExperience');
    Route::post('/store-user-dependants', 'UserController@storeUserDependants');
    Route::post('/store-user-extras', 'UserController@storeUserExtras');
    Route::get('/hr-biodata-preview', 'hrController@displayBiodata');
    Route::get('hr-start-prs-session', 'PrsSessionController@startPrs');
    Route::get('{user}/job-description', 'PrsSessionController@jobDescription');
    Route::get('{user}/performance-review', 'PrsSessionController@performanceReview');
    Route::get('{user}/ecdp', 'PrsSessionController@ecdp');
    Route::get('hr-user-job-description', 'PrsSessionController@hrUserJd');
    Route::get('hr-user-review', 'PrsSessionController@hrUserReview');

    
    Route::get('/update-transporter-rate-at-topup/{tripId}', 'overviewController@updateTransporterRateTopup');
    Route::get('notification-real-data', 'backendController@realTimeEvent');
    Route::get('real-stat', 'backendController@realStat');
    Route::get('completed/not-drop-off', 'IssueTypeController@completedNotDropOff');
    Route::get('update-semi-trip-location', 'IssueTypeController@updateSemitripLocation');
    Route::get('drop-off-completed', 'IssueTypeController@dropOffCompleted');

    Route::get('/badge-trips', 'BadgingController@showTrips');
    Route::post('/badge-truck', 'BadgingController@badgeTruck');
    Route::post('/remove-badge-truck', 'BadgingController@removeBadgedTruck');
    Route::get('/last-trip-id', 'dashboardController@lastTripId');
    Route::get('/trip-finders', 'dashboardController@tripFinders');
    Route::get('trip-finder-search', 'dashboardController@searchTripFinder');
    Route::get('trip-status-result', 'dashboardController@tripStatusResult');

    Route::get('/client-payment-model', 'trackerController@clientPaymentModel');

    Route::get('/financial/report', 'FinancialReportController@financialReporting');
    Route::get('rt-notification/{time}', 'dashboardController@realTimeNotification');
    Route::get('/finance/waybill-status', 'FinancialReportController@waybillStatus');
    Route::get('/finance/unpaid-invoices', 'FinancialReportController@unpaidInvoices');
    Route::get('/finance/paid-invoices', 'FinancialReportController@paidInvoices');
    Route::get('/finance/uninvoiced-trips', 'FinancialReportController@uninvoicedTrips');
    Route::get('/finance/invoiced-trips', 'FinancialReportController@invoicedTrips');
    Route::get('/finance/transporter-account', 'FinancialReportController@transporterAccount');
    Route::get('/finance/outstanding-bills', 'FinancialReportController@outstandingBills');
    Route::get('/finance/trip-search', 'FinancialReportController@tripSearch');

    Route::get('/update-operations-remark', 'dashboardController@updateOperationsRemark');
    Route::get('/truck-availability-data', 'dashboardController@truckAvailabilityData');
    Route::get('/today-gate-out-data', 'dashboardController@todayGateOut');

    Route::get('transloaded-trip-info', 'IssueTypeController@exactTrip');
    Route::post('truck-transload', 'IssueTypeController@transloadTruck');
    Route::get('update-drivers-info', 'IssueTypeController@changeDriverInfo');
    Route::get('decline-advance-request', 'paymentExceptionController@declineAdvanceRequest');
    Route::get('decline-balance-request', 'paymentExceptionController@declineBalanceRequest');
    Route::get('camt/client-target', 'camtController@clientTargetSetter');
    Route::post('camt/client-account-target', 'camtController@clientAccountTarget');
    Route::get('camt/client-account-manager', 'camtController@clientAccountManager');
    Route::post('assign-client-account-manager', 'camtController@assignClientAccountManager');
    Route::post('remove-assigned-account-manager', 'camtController@removeAssignedAccountManager');

    Route::get('orders-loading-sites', 'sortController@orderLoadingSites');

    Route::get('trips/client/all', 'sortController@clientAll');
    Route::get('trips/client/date-range-client-and-status', 'sortController@dateRangeClientAndStatus');
    Route::get('trips/client/date-range-and-client', 'sortController@dateRangeAndClient');
    Route::get('trips/client/client-loading-site-and-trip-status', 'sortController@clientLoadingSiteAndTripStatus');
    Route::get('trips/client/client-and-status', 'sortController@clientAndStatus');
    Route::get('trips/client/dateRangeClientStatus', 'sortController@dateRangeClientStatus');

    Route::get('trips/transporters/transporter', 'sortController@transporterOnly');
    Route::get('trips/transporters/transporterAndDateRange', 'sortController@transporterAndDateRange');
    Route::get('trips/transporters/all', 'sortController@transporterAll');
    Route::get('trips/transporters/transporterAndTripStatus', 'sortController@transporterAndTripStatus');
    Route::get('trips/trips/voided', 'sortController@voidedTrips');
    Route::get('trips/trips/trip-status', 'sortController@tripsTripStatus');

    Route::get('payment-notification-history', 'PaymentNotificationController@paymentNotifications');
    Route::get('approve-uploaded-payment', 'PaymentNotificationController@approveUploadedPayment');
    Route::get('decline-uploaded-payment', 'PaymentNotificationController@declineUploadedPayment');
    Route::post('/upload-eirs', 'dashboardController@UploadEirs');

    Route::get('change-invoice-status', 'invoiceController@changeInvoiceStatus');
    Route::get('buh-transporter-gained', 'performanceMetricController@transporterGained');
    Route::get('update-po-number', 'invoiceController@updatePoNumber');

    Route::get('buh-trips-breakdown', 'performanceMetricController@tripsBreakdown');
    Route::get('expenses-breakdown', 'trackerController@showExpensesBreakdown');
    Route::get('performance-analysis', 'performanceMetricController@performanceAnalysis');

    Route::get('available-incentives', 'overviewController@tripIncentives');
    Route::post('add-incentives', 'overviewController@addIncentives');
    Route::get('remove-incentive/{incentiveId}', 'overviewController@removeIncentive');

    Route::get('opex-update', 'expensesController@opex');
    Route::get('opex-listings', 'expensesController@showOpex');
    Route::get('bonus-breakdown', 'performanceMetricController@getBonusBreakDown');
    Route::resource('payment-voucher-request', 'PaymentVoucherController');
    Route::get('verify-payment-voucher', 'PaymentVoucherController@verifyPaymentVoucher');
    Route::patch('verify-payment-voucher', 'PaymentVoucherController@verifyPayments');
    Route::get('payment-voucher-approvals', 'PaymentVoucherController@getPaymentVoucherApprovals');
    Route::patch('approve-payment-voucher', 'PaymentVoucherController@approvePaymentVouchers');
    Route::get('payment-vouchers', 'PaymentVoucherController@vouchers');
    Route::get('payment-voucher/{voucherId}/{encryptedVoucherId}', 'PaymentVoucherController@showPaymentVoucher');
    Route::patch('upload-payment-voucher', 'PaymentVoucherController@uploadPaymentVoucher');
    Route::get('invoice-payment-history', 'invoiceController@getInvoicePaymentHistory');
    Route::get('deline-payment-voucher', 'PaymentVoucherController@declinePaymentVoucher');
    Route::get('flag-voucher', 'PaymentVoucherController@strikeOutVoucher');
    Route::get('/delete-voucher-request', 'PaymentVoucherController@deleteVoucher');
    Route::get('/unlink-signed-eir', 'ordersController@unlinkSignedWaybill');
    Route::post('/upload-signed-waybill', 'ordersController@uploadSignedEir');
    Route::get('remove-invoice-payment-history', 'invoiceController@deletePaymentBreakDown');
    Route::get('pair-field-ops-loading-site', 'fieldOpsLoadingSiteController@pairfiedOpsLoadingSite');
    Route::get('loading-site-person-pair', 'fieldOpsLoadingSiteController@fetchLoadingSitePersonPair');
    Route::post('pair-person-loading-site', 'fieldOpsLoadingSiteController@pairPersonLoadingSite');
    Route::post('remove-paired-loading-site', 'fieldOpsLoadingSiteController@removePairedLoadingSite');
    Route::get('generate-transport-team-report', 'performanceReportGenerator@generateReport');

    Route::get('invoice-more-incentives', 'invoiceController@addMoreIncentivesOnInvoice');
    Route::post('add-more-incentive-on-invoice', 'invoiceController@addMoreIncentiveOnInvoice');
    Route::get('remove-added-incentive-on-invoice', 'invoiceController@removeAddedIncentiveOnInvoice');
    Route::get('send-payment-notification', 'overviewController@sendPaymentNotification');
    Route::resource('expense-type', 'ExpenseTypeController');
    Route::get('department-expense-type', 'ExpenseTypeController@departmentExpenseType');
    Route::post('assign-department-expense-type', 'ExpenseTypeController@assignDepartmentExpenseType');
    Route::post('remove-assigned-department-expense-type', 'ExpenseTypeController@removeDepartmentExpenseType');
    Route::resource('department', 'DepartmentController');
    Route::get('expenses-category-breakdown', 'trackerController@expenseCategoryBreakdown');
    Route::post('add-refund-beneficiary', 'PaymentVoucherController@newBeneficiary');

    // Kaya Pay
    Route::resource('kaya-pay-agreements', 'ClientArrangementController');

});



