<?php
	defined('BASEPATH') OR exit('No direct script access allowed');
	
	/*
	| -------------------------------------------------------------------------
	| URI ROUTING
	| -------------------------------------------------------------------------
	| This file lets you re-map URI requests to specific controller functions.
	|
	| Typically there is a one-to-one relationship between a URL string
	| and its corresponding controller class/method. The segments in a
	| URL normally follow this pattern:
	|
	|	example.com/class/method/id/
	|
	| In some instances, however, you may want to remap this relationship
	| so that a different class/function is called than the one
	| corresponding to the URL.
	|
	| Please see the user guide for complete details:
	|
	|	https://codeigniter.com/user_guide/general/routing.html
	|
	| -------------------------------------------------------------------------
	| RESERVED ROUTES
	| -------------------------------------------------------------------------
	|
	| There are three reserved routes:
	|
	|	$route['default_controller'] = 'welcome';
	|
	| This route indicates which controller class should be loaded if the
	| URI contains no data. In the above example, the "welcome" class
	| would be loaded.
	|
	|	$route['404_override'] = 'errors/page_missing';
	|
	| This route will tell the Router which controller/method to use if those
	| provided in the URL cannot be matched to a valid route.
	|
	|	$route['translate_uri_dashes'] = FALSE;
	|
	| This is not exactly a route, but allows you to automatically route
	| controller and method names that contain dashes. '-' isn't a valid
	| class or method name character, so it requires translation.
	| When you set this option to TRUE, it will replace ALL dashes in the
	| controller and method URI segments.
	|
	| Examples:	my-controller/index	-> my_controller/index
	|		my-controller/my-method	-> my_controller/my_method
	*/
	$route['default_controller'] = 'home';
	$route['404_override'] = 'home/error_404';
	$route['translate_uri_dashes'] = FALSE;
	
	$route['login'] = 'home/auth_login';
	$route['forgot_password'] = 'home/forgot_password';
	$route['forgot_password_action'] = 'home/forgot_password_action';
	$route['reset_password'] = 'home/reset_password';
	$route['logout'] = 'home/logout';
	$route['access_denied'] = 'home/access_denied';
	$route['error_404'] = 'home/error_404';
	$route['timestamp'] = 'home/timestamp';
	
	$route['income_stats'] = 'home/get_income_statistics';
	$route['deduction_stats'] = 'home/get_deduction_statistics';
	
	$route['bank'] = 'hr_configuration/bank';
	$route['add_bank'] = 'hr_configuration/add_bank';
	$route['update_bank'] = 'hr_configuration/update_bank';
	
	$route['pension'] = 'hr_configuration/pension';
	$route['add_pension'] = 'hr_configuration/add_pension';
	$route['update_pension'] = 'hr_configuration/update_pension';
	
	$route['health_insurance'] = 'hr_configuration/health_insurance';
	$route['add_health_insurance'] = 'hr_configuration/add_health_insurance';
	$route['update_health_insurance'] = 'hr_configuration/update_health_insurance';
	
	$route['test'] = 'hr_configuration/test';
	
	$route['location'] = 'hr_configuration/location';
	$route['add_location'] = 'hr_configuration/add_location';
	$route['update_location'] = 'hr_configuration/update_location';
	
	$route['subsidiary'] = 'hr_configuration/subsidiary';
	$route['add_subsidiary'] = 'hr_configuration/add_subsidiary';
	$route['update_subsidiary'] = 'hr_configuration/update_subsidiary';
	
	$route['leave'] = 'hr_configuration/leave';
	$route['add_leave'] = 'hr_configuration/add_leave';
	$route['update_leave'] = 'hr_configuration/update_leave';
	
	
	$route['grade'] = 'hr_configuration/grade';
	$route['add_grade'] = 'hr_configuration/add_grade';
	$route['update_grade'] = 'hr_configuration/update_grade';
	
	$route['qualification'] = 'hr_configuration/qualification';
	$route['add_qualification'] = 'hr_configuration/add_qualification';
	$route['update_qualification'] = 'hr_configuration/update_qualification';
	
	
	$route['department'] = 'hr_configuration/department';
	$route['add_department'] = 'hr_configuration/add_department';
	$route['update_department'] = 'hr_configuration/update_department';
	
	$route['job_role'] = 'hr_configuration/job_role';
	$route['add_job_role'] = 'hr_configuration/add_job_role';
	$route['update_job_role'] = 'hr_configuration/update_job_role';

//appraisal
	$route['appraisal_setup'] = 'hr_configuration/appraisal_setup';
	$route['self_assessment'] = 'hr_configuration/self_assessment';
	$route['add_self_assessment'] = 'hr_configuration/add_self_assessment';
	$route['update_self_assessment'] = 'hr_configuration/update_self_assessment';
	
	$route['appraisals'] = 'employee_main/appraisals';
	
	
	$route['quantitative_assessment'] = 'hr_configuration/quantitative_assessment';
	$route['add_quantitative_assessment'] = 'hr_configuration/add_quantitative_assessment';
	$route['view_quantitative_assessment/:num'] = 'hr_configuration/view_quantitative_assessment/$1';
	$route['update_quantitative_assessment'] = 'hr_configuration/update_quantitative_assessment';
	
	
	$route['qualitative_assessment'] = 'hr_configuration/qualitative_assessment';
	$route['add_qualitative_assessment'] = 'hr_configuration/add_qualitative_assessment';
	$route['update_qualitative_assessment'] = 'hr_configuration/update_qualitative_assessment';
	
	$route['supervisor_assessment'] = 'hr_configuration/supervisor_assessment';
	$route['add_supervisor_assessment'] = 'hr_configuration/add_supervisor_assessment';
	$route['update_supervisor_assessment'] = 'hr_configuration/update_supervisor_assessment';
	
	$route['trainings'] = 'hr_configuration/trainings';
	$route['new_training'] = 'hr_configuration/new_training';
	$route['upload_training_materials'] = 'hr_configuration/upload_training_materials';
	$route['add_training'] = 'hr_configuration/add_training';
	$route['edit_training/:num'] = 'hr_configuration/edit_training/$1';
	$route['remove_material'] = 'hr_configuration/remove_material';
	$route['update_training'] = 'hr_configuration/update_training';
	$route['training_questions/:num'] = 'hr_configuration/training_questions/$1';
	$route['add_question'] = 'hr_configuration/add_question';
	$route['update_question'] = 'hr_configuration/update_question';
	$route['delete_question'] = 'hr_configuration/delete_question';
	$route['view_training/:num'] = 'hr_configuration/view_training/$1';
	$route['hr_documents'] = 'hr_configuration/hr_documents';
	$route['add_hr_document'] = 'hr_configuration/add_hr_document';
	$route['view_hr_document/:num'] = 'hr_configuration/view_hr_document/$1';
	$route['delete_hr_document/:num'] = 'hr_configuration/delete_hr_document/$1';
	
	
	$route['user'] = 'user/user';
	$route['new_user'] = 'user/new_user';
	$route['add_user'] = 'user/add_user';
	$route['manage_user/:num'] = 'user/manage_user/$1';
	$route['edit_user'] = 'user/edit_user';
	
	$route['employee'] = 'employee/employee';
	$route['new_employee'] = 'employee/new_employee';
	$route['add_employee'] = 'employee/add_employee';
	$route['get_employees'] = 'employee/get_employees';
	$route['employee_upload_others'] = 'employee/employee_upload_others';
	$route['view_employee/:num'] = 'employee/view_employee/$1';
	$route['update_employee/:num'] = 'employee/update_employee/$1';
	$route['edit_employee'] = 'employee/edit_employee';
	$route['employee_transfer'] = 'employee/employee_transfer';
	$route['new_employee_transfer'] = 'employee/new_employee_transfer';
	$route['add_new_employee_transfer'] = 'employee/add_new_employee_transfer';
	$route['employee_leave'] = 'employee/employee_leave';
	$route['new_employee_leave'] = 'employee/new_employee_leave';
	$route['add_new_employee_leave'] = 'employee/add_new_employee_leave';
	$route['extend_leave/:num'] = 'employee/extend_leave/$1';
	$route['approve_leave/:num'] = 'employee/approve_employee_leave/$1';
	$route['discard_leave/:num'] = 'employee/discard_employee_leave/$1';
	
	$route['extend_employee_leave'] = 'employee/extend_employee_leave';
	$route['employee_appraisal'] = 'employee/employee_appraisal';
	$route['new_employee_appraisal'] = 'employee/new_employee_appraisal';
	$route['check_appraisal_result/:num'] = 'employee/check_appraisal_result/$1';
	$route['reassign_supervisor/:num'] = 'employee/reassign_supervisor/$1';
	$route['add_new_employee_appraisal'] = 'employee/add_new_employee_appraisal';
	$route['terminate_employee/:num'] = 'employee/terminate_employee/$1';
	$route['update_employee_appraisal'] = 'employee/update_employee_appraisal';
	$route['terminate'] = 'employee/terminate';
	$route['terminations'] = 'employee/terminations';
	$route['resignations'] = 'employee/resignations';
	$route['approve_resignation/:num'] = 'employee/approve_resignation/$1';
	$route['discard_resignation/:num'] = 'employee/discard_resignation/$1';
	$route['employee_queries'] = 'employee/employee_queries';
	$route['query_employee/:num'] = 'employee/query_employee/$1';
	$route['new_query'] = 'employee/new_query';
	$route['view_query/:num'] = 'employee/view_query/$1';
	$route['new_response'] = 'employee/new_response';
	$route['close_query/:num'] = 'employee/close_query/$1';
	$route['memo'] = 'employee/memo';
	$route['add_memo'] = 'employee/add_memo';
	$route['update_memo'] = 'employee/update_memo';
	$route['specific_memo'] = 'employee/specific_memo';
	$route['new_specific_memo'] = 'employee/new_specific_memo';
	$route['add_specific_memo'] = 'employee/add_specific_memo';
	$route['update_specific_memo'] = 'employee/update_specific_memo';
	$route['employee_trainings'] = 'employee/employee_trainings';
	$route['new_employee_training'] = 'employee/new_employee_training';
	$route['add_new_employee_training'] = 'employee/add_new_employee_training';
	$route['view_training_result/:num'] = 'employee/view_training_result/$1';
	$route['clear_notifications/(:any)'] = 'employee/clear_notifications/$1';
	
	
	$route['tax_rates'] = 'payroll_configuration/tax_rate';
	$route['add_tax_rate'] = 'payroll_configuration/add_tax_rate';
	$route['update_tax_rate'] = 'payroll_configuration/update_tax_rate';
	
	
	$route['payment_definition'] = 'payroll_configuration/payment_definition';
	$route['add_payment_definition'] = 'payroll_configuration/add_payment_definition';
	$route['new_payment_definition'] = 'payroll_configuration/new_payment_definition';
	$route['edit_payment_definition/:num'] = 'payroll_configuration/edit_payment_definition/$1';
	$route['update_payment_definition'] = 'payroll_configuration/update_payment_definition';
	
	$route['salary_structure'] = 'payroll_configuration/salary_structure';
	$route['add_salary_structure'] = 'payroll_configuration/add_salary_structure';
	$route['update_salary_structure'] = 'payroll_configuration/update_salary_structure';
	$route['view_salary_structure/:num'] = 'payroll_configuration/view_salary_structure/$1';
	
	$route['allowance'] = 'payroll_configuration/allowance';
	$route['new_salary_allowance'] = 'payroll_configuration/new_salary_allowance';
	$route['add_salary_allowance'] = 'payroll_configuration/add_salary_allowance';
	$route['edit_salary_allowance/:num'] = 'payroll_configuration/edit_salary_allowance/$1';
	$route['update_salary_allowance'] = 'payroll_configuration/update_salary_allowance';
	$route['setup_salary_structure/:num'] = 'payroll_configuration/setup_salary_structure/$1';
	$route['min_tax_rate'] = 'payroll_configuration/min_tax_rate';
	$route['add_min_tax_rate'] = 'payroll_configuration/add_min_tax_rate';
	$route['update_min_tax_rate'] = 'payroll_configuration/update_min_tax_rate';
	$route['pension_rate'] = 'payroll_configuration/pension_rate';
	$route['add_pension_rate'] = 'payroll_configuration/add_pension_rate';
	$route['update_pension_rate'] = 'payroll_configuration/update_pension_rate';
	
	
	$route['payroll_month_year'] = 'payroll_configuration/payroll_month_year';
	$route['add_payroll_month_year'] = 'payroll_configuration/add_payroll_month_year';
	$route['update_payroll_month_year'] = 'payroll_configuration/update_payroll_month_year';
	$route['variational_payment'] = 'payroll/variational_payment';
	$route['new_variational_payment'] = 'payroll/new_variational_payment';
	$route['add_variational_payment'] = 'payroll/add_variational_payment';
	$route['recall_month'] = 'payroll/recall_month';
	
	$route['employee_salary_structure'] = 'payroll/employee_salary_structure';
	$route['add_employee_salary_structure'] = 'payroll/add_employee_salary_structure';
	$route['view_employee_salary_structure/:num'] = 'payroll/view_employee_salary_structure/$1';
	$route['edit_employee_salary_structure/:num'] = 'payroll/edit_employee_salary_structure/$1';
	$route['update_employee_salary_structure'] = 'payroll/update_employee_salary_structure/$1';
	$route['approve_variational_payment'] = 'payroll/approve_variational_payment';
	$route['approve_variational_payments'] = 'payroll/approve_variational_payments';
	
	$route['new_loan'] = 'loan/new_loan';
	$route['add_new_loan'] = 'loan/add_new_loan';
	$route['loans'] = 'loan/loans';
	$route['edit_loan/:num'] = 'loan/edit_loan/$1';
	$route['update_loan'] = 'loan/update_loan';
	$route['approve_loan/:num'] = 'loan/approve_loan/$1';
	$route['discard_loan/:num'] = 'loan/discard_loan/$1';
	
	$route['payroll_routine'] = 'payroll/payroll_routine';
	$route['run_payroll_routine'] = 'payroll/run_payroll_routine';
	$route['approve_payroll_routine'] = 'payroll/approve_payroll_routine';
	$route['undo_payroll_routine'] = 'payroll/undo_payroll_routine';
	$route['run_approve_payroll_routine'] = 'payroll/run_approve_payroll_routine';
	$route['payroll_report'] = 'payroll_report/index';
	$route['emolument'] = 'payroll_report/emolument';
	$route['emolument_report'] = 'payroll_report/emolument_report';
	$route['emolument_report_clear'] = 'payroll_report/emolument_report_clear';
	$route['deduction'] = 'payroll_report/deduction';
	$route['deduction_report'] = 'payroll_report/deduction_report';
	$route['pay_order'] = 'payroll_report/pay_order';
	$route['pay_order_report'] = 'payroll_report/pay_order_report';
	
	$route['enroll_employee'] = 'biometrics/enroll_employee';
//$route['checkreg/:num/(:any)'] = 'biometrics/checkreg/$1/$2';
	$route['checkreg'] = 'biometrics/checkreg';
	$route['reg/:num'] = 'biometrics/reg/$1';
	$route['messages/(:any)'] = 'biometrics/messages/$1';
	$route['message/(:any)'] = 'biometrics/message/$1';
	$route['getac'] = 'biometrics/getac';
	$route['clock_in/:num'] = 'biometrics/clock_in/$1';
	$route['clockin'] = 'biometrics/clockin';
	$route['clock_attendance'] = 'biometrics/clock_attendance';
	$route['process_register'] = 'biometrics/process_register';
	$route['process_verification'] = 'biometrics/process_verification';
	$route['biometrics_report'] = 'biometrics/biometrics_report';
	$route['today_present'] = 'biometrics/today_present';
	$route['today_absent'] = 'biometrics/today_absent';
	$route['present_employee'] = 'biometrics/present_employee';
	$route['present_employeee'] = 'biometrics/present_employeee';
	$route['absent_employee'] = 'biometrics/absent_employee';
	$route['absent_employeee'] = 'biometrics/absent_employeee';
	$route['message_clockout/(:any)'] = 'biometrics/message_clockout/$1';
	$route['messages_clockout/(:any)'] = 'biometrics/messages_clockout/$1';
	$route['process_clockout'] = 'biometrics/process_clockout';
	$route['clockout_attendance'] = 'biometrics/clockout_attendance';
	$route['clock_out/:num'] = 'biometrics/clock_out/$1';
	$route['clock_out_today'] = 'biometrics/clock_out_today';
	$route['clock_out_date'] = 'biometrics/clock_out_date';
	$route['clock_out_datee'] = 'biometrics/clock_out_datee';
	
	
	
	
	$route['view_log'] = 'log/view_log';
	
	
	$route['employee_history'] = 'employee_main/employee_history';
	$route['request_leave'] = 'employee_main/request_leave';
	$route['request_new_leave'] = 'employee_main/request_new_leave';
	$route['my_leave'] = 'employee_main/my_leave';
	$route['appraise_employee'] = 'employee_main/appraise_employee';
	$route['respond_appraisal_supervisor/:num'] = 'employee_main/respond_appraisal_supervisor/$1';
	$route['answer_questions_supervisor'] = 'employee_main/answer_questions_supervisor';
	$route['respond_appraisal_self/:num'] = 'employee_main/respond_appraisal_self/$1';
	$route['answer_questions_self'] = 'employee_main/answer_questions_self';
	$route['appraisal_result/:num'] = 'employee_main/check_appraisal_results/$1';
	$route['pay_slip'] = 'employee_main/pay_slip';
	$route['pay_slips'] = 'employee_main/pay_slips';
	$route['my_loan'] = 'employee_main/my_loan';
	$route['my_new_loan'] = 'employee_main/my_new_loan';
	$route['apply_loan'] = 'employee_main/apply_loan';
	$route['employee_resignation'] = 'employee_main/employee_resignation';
	$route['resignation'] = 'employee_main/resignation';
	$route['my_queries'] = 'employee_main/my_queries';
	$route['view_my_query/:num'] = 'employee_main/view_my_query/$1';
	$route['personal_information'] = 'employee_main/personal_information';
	$route['my_memos'] = 'employee_main/my_memos';
	$route['my_specific_memos'] = 'employee_main/my_specific_memos';
	$route['update_notification'] = 'employee_main/update_notification';
	$route['view_notification/:num'] = 'employee_main/view_notification/$1';
	$route['view_notifications/:num'] = 'employee/view_notifications/$1';
	$route['my_trainings'] = 'employee_main/my_trainings';
	$route['begin_training/:num/:num'] = 'employee_main/begin_training/$1/$1';
	$route['start_test/:num/:num'] = 'employee_main/start_test/$1/$1';
	$route['score_test'] = 'employee_main/score_test';
	$route['update_time'] = 'employee_main/update_time';
	$route['check_training_result/:num'] = 'employee_main/check_training_result/$1';
	$route['my_chat'] = 'employee_main/my_chat';
	$route['send_chat'] = 'employee_main/send_chat';
	$route['get_chats'] = 'employee_main/get_chats';
	$route['get_online'] = 'employee_main/get_online';
	$route['documents'] = 'employee_main/documents';
	$route['view_document/:num'] = 'employee_main/view_document/$1';
	$route['get_income_payments'] = 'employee_main/get_income_payments';
	$route['get_deduction_payments'] = 'employee_main/get_deduction_payments';
	
	$route['change_password'] = 'employee_main/change_password';
	$route['change_password_'] = 'employee_main/change_password_';
	$route['get_notifications'] = 'employee_main/get_notifications';
	$route['new_task'] = 'employee_main/new_task';
	$route['my_tasks'] = 'employee_main/my_tasks';
	$route['assigned_tasks'] = 'employee_main/assigned_tasks';
	$route['view_task/:num'] = 'employee_main/view_task/$1';
	$route['new_task_response'] = 'employee_main/new_task_response';
	$route['close_task/:num'] = 'employee_main/close_task/$1';
	$route['employee_clockin'] = 'employee_main/clockin';
	$route['employee_clockout'] = 'employee_main/clockout';
	
	
	
	$route['employee_tax'] = 'payroll/employee_tax';

// hr reports
	$route['hr_report'] = 'hr_report/index';
	$route['top_performer'] = 'hr_report/top_performer';
	$route['new_hire'] = 'hr_report/new_hire';
	$route['retention'] = 'hr_report/retention';
	$route['turn_over'] = 'hr_report/turn_over';






