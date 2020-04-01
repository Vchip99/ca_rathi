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

Route::get('/', 'HomeController@index');
Route::post('enquiry', 'HomeController@enquiry');

Route::group(['prefix' => 'admin'], function () {
  Route::get('/login', 'AdminAuth\LoginController@showLoginForm')->name('login');
  Route::post('/login', 'AdminAuth\LoginController@login');
  Route::post('/logout', 'AdminAuth\LoginController@logout')->name('logout');

  // Route::get('/register', 'AdminAuth\RegisterController@showRegistrationForm')->name('register');
  // Route::post('/register', 'AdminAuth\RegisterController@register');

  // Route::post('/password/email', 'AdminAuth\ForgotPasswordController@sendResetLinkEmail')->name('password.request');
  // Route::post('/password/reset', 'AdminAuth\ResetPasswordController@reset')->name('password.email');
  // Route::get('/password/reset', 'AdminAuth\ForgotPasswordController@showLinkRequestForm')->name('password.reset');
  // Route::get('/password/reset/{token}', 'AdminAuth\ResetPasswordController@showResetForm');

  Route::get('/home', 'Admin\AdminController@home');
  Route::get('/enquiries', 'Admin\AdminController@enquiries');
  Route::delete('/delete-enquiry', 'Admin\AdminController@deleteEnquiry');
  Route::get('create-enquiry', 'Admin\AdminController@createEnquiry');
  Route::post('create-enquiry', 'Admin\AdminController@storeEnquiry');
  Route::get('enquiry/{id}/edit', 'Admin\AdminController@editEnquiry');
  Route::post('get-enquiry-by-course', 'Admin\AdminController@getEnquiryByCourse');

  // Route::get('/create-student-admission', 'Admin\AdmissionController@createAdmission');
  // Route::post('/create-student-admission', 'Admin\AdmissionController@storeAdmission');
  Route::get('/create-admission-receipt', 'Admin\AdmissionController@create');
  Route::post('/create-admission-receipt', 'Admin\AdmissionController@store');
  Route::get('/course-payment/{id}/edit', 'Admin\AdmissionController@edit');
  Route::delete('/delete-course-payment', 'Admin\AdmissionController@delete');
  Route::post('/get-users-by-sub-courses-id', 'Admin\AdmissionController@getUsersBySubCourseId');
  Route::get('/show-student-receipt/{id}', 'Admin\AdmissionController@showReceipt');
  Route::get('/download-course-payments', 'Admin\AdmissionController@downloadCoursePayments');
  Route::get('/manage-course-payment', 'Admin\AdmissionController@showCoursePayments');
  Route::post('/get-user-by-user-id', 'Admin\AdmissionController@getUserByUserId');
  Route::post('/get-user-payments-by-courseId-by-subcourseId-by-batchId', 'Admin\AdmissionController@getUserPaymentsByCourseIdBySubcourseIdByBatchId');
  Route::post('/get-user-total-paid-by-course-id-by-sub-course-id-by-batch-id', 'Admin\AdmissionController@getUserTotalPaidByCourseIdBySubcourseIdByBatchId');
  Route::post('/get-user-total-paid-by-course-id-by-sub-course-id-by-batch-id-for-payments', 'Admin\AdmissionController@getUserTotalPaidByCourseIdBySubcourseIdByBatchIdForPayments');
  Route::post('/toggle-records', 'Admin\AdmissionController@toggleRecords');
  Route::get('/delete-payments', 'Admin\AdmissionController@showDeletePayments');
  Route::delete('/delete-payments', 'Admin\AdmissionController@deletePayments');
  Route::get('/outstanding', 'Admin\AdmissionController@outstanding');
  Route::post('/get-outstanding-by-course-id-by-sub-course-id-by-batch-id', 'Admin\AdmissionController@getOutstandingByCourseIdBySubCourseIdByBatchId');
  Route::put('/update-admission-receipt', 'Admin\AdmissionController@update');
  Route::get('/download-outstandings', 'Admin\AdmissionController@downloadOutstandings');
  Route::get('/userCourses', 'Admin\AdmissionController@userCourses');
  Route::post('get-user-course-payments', 'Admin\AdmissionController@getUserCoursePayments');

  // admin formation
  Route::get('/manage-admin', 'Admin\SubAdminController@show');
  Route::get('/create-admin', 'Admin\SubAdminController@create');
  Route::post('/create-admin', 'Admin\SubAdminController@store');
  Route::get('/admin/{id}/edit', 'Admin\SubAdminController@edit');
  Route::put('/update-admin', 'Admin\SubAdminController@update');
  Route::delete('/delete-admin', 'Admin\SubAdminController@delete');
  Route::post('/get-sub-courses-by-id', 'Admin\SubAdminController@getSubCoursesByCourseId');
  Route::post('/is-sub-course-used', 'Admin\SubAdminController@isSubCoursesUsed');
  Route::post('/get-subcourse-details', 'Admin\SubAdminController@getCourseReceipt');
  Route::get('/manage-password', 'Admin\SubAdminController@showPassword');
  Route::put('/update-admin-password', 'Admin\SubAdminController@updateAdminPassword');


  // course formation
  Route::get('manage-course', 'Admin\CourseController@show');
  Route::get('create-course', 'Admin\CourseController@create');
  Route::post('create-course', 'Admin\CourseController@store');
  Route::get('course/{id}/edit', 'Admin\CourseController@edit');
  Route::put('update-course', 'Admin\CourseController@update');
  Route::delete('delete-course', 'Admin\CourseController@delete');

  // sub course formation
  Route::get('manage-subcourse', 'Admin\SubCourseController@show');
  Route::get('create-subcourse', 'Admin\SubCourseController@create');
  Route::post('create-subcourse', 'Admin\SubCourseController@store');
  Route::get('subcourse/{id}/edit', 'Admin\SubCourseController@edit');
  Route::put('update-subcourse', 'Admin\SubCourseController@update');
  Route::delete('delete-subcourse', 'Admin\SubCourseController@delete');
  Route::post('/get-sub-courses-by-id', 'Admin\SubCourseController@getSubCoursesByCourseId');

  // batch formation
  Route::get('manage-batch', 'Admin\BatchController@show');
  Route::get('create-batch', 'Admin\BatchController@create');
  Route::post('create-batch', 'Admin\BatchController@store');
  Route::get('batch/{id}/edit', 'Admin\BatchController@edit');
  Route::put('update-batch', 'Admin\BatchController@update');
  Route::delete('delete-batch', 'Admin\BatchController@delete');
  Route::post('get-batches-by-course-id-by-sub-course-id', 'Admin\BatchController@getBatchesByCourseIdBySubCourseId');

  // discount formation
  Route::get('manage-discount', 'Admin\DiscountController@show');
  Route::get('create-discount', 'Admin\DiscountController@create');
  Route::post('create-discount', 'Admin\DiscountController@store');
  Route::get('discount/{id}/edit', 'Admin\DiscountController@edit');
  Route::delete('delete-discount', 'Admin\DiscountController@delete');
  Route::post('get-users-by-course-id-by-sub-course-id-by-batch-id', 'Admin\DiscountController@getUsersByCourseIdBySubCourseIdByBatchId');

  // refund formation
  Route::get('manage-refund', 'Admin\RefundController@show');
  Route::get('create-refund', 'Admin\RefundController@create');
  Route::post('create-refund', 'Admin\RefundController@store');
  Route::get('refund/{id}/edit', 'Admin\RefundController@edit');
  Route::delete('delete-refund', 'Admin\RefundController@delete');

});
