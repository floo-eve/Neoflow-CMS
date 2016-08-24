<?php
$this->addNamespace('\\Neoflow\\CMS\\Controller\\');
$this->addNamespace('\\Neoflow\\CMS\\Controller\\Backend\\');

// Backend routes
$this->addRoutes(array(
    array('backend_index', 'any', '/backend', 'Backend'),
    array('backend_login', 'get', '/backend/login', 'Backend@login'),
    array('backend_logout', 'get', '/backend/logout', 'Backend@logout'),
    array('backend_auth', 'post', '/backend/auth', 'Backend@auth'),
    array('backend_lost_password', 'get', '/backend/lost-password', 'Backend@lostPassword'),
    array('backend_reset_password', 'post', '/backend/reset-password', 'Backend@resetPassword'),
    array('backend_new_password', 'get', '/backend/new-password/(reset_key:string)', 'Backend@newPassword'),
    array('backend_update_password', 'post', '/backend/update-password', 'Backend@updatePassword'),
));


// Backend dashboard routes
$this->addRoutes(array(
    array('dashboard_index', 'get', '/backend/dashboard', 'Dashboard')
));

// Backend page routes
$this->addRoutes(array(
    array('page_edit', 'get', '/backend/page/edit/(id:num)', 'Page@edit'),
    array('page_sections', 'get', '/backend/page/sections/(id:num)', 'Page@sections'),
    array('page_delete', 'get', '/backend/page/delete/(id:num)', 'Page@delete'),
    array('page_activate', 'get', '/backend/page/activate/(id:num)', 'Page@activate'),
    array('page_update', 'post', '/backend/page/update', 'Page@update'),
    array('page_create', 'post', '/backend/page/create', 'Page@create'),
    array('page_index', 'get', '/backend/pages', 'Page@index')
));

// Backend section routes
$this->addRoutes(array(
    array('section_reorder', 'post', '/backend/section/reorder', 'Section@reorder'),
    array('section_create', 'post', '/backend/section/create', 'Section@create'),
    array('section_delete', 'get', '/backend/section/delete/(id:num)', 'Section@delete'),
    array('section_activate', 'get', '/backend/section/activate/(id:num)', 'Section@activate')
));

// Backend user routes
$this->addRoutes(array(
    array('user_create', 'post', '/backend/user/create', 'User@create'),
    array('user_edit', 'get', '/backend/user/edit/(id:num)', 'User@edit'),
    array('user_delete', 'get', '/backend/user/delete/(id:num)', 'User@delete'),
    array('user_update', 'post', '/backend/user/update', 'User@update'),
    array('user_update_password', 'post', '/backend/user/update-password', 'User@updatePassword'),
    array('user_index', 'get', '/backend/users', 'User@index')
));

// Backend role routes
$this->addRoutes(array(
    array('role_create', 'post', '/backend/role/create', 'Role@create'),
    array('role_edit', 'get', '/backend/role/edit/(id:num)', 'Role@edit'),
    array('role_delete', 'get', '/backend/role/delete/(id:num)', 'Role@delete'),
    array('role_update', 'post', '/backend/role/update', 'Role@update'),
    array('role_index', 'get', '/backend/roles', 'Role@index')
));


// Backend navigation routes
$this->addRoutes(array(
    array('navigation_create', 'post', '/backend/navigation/create', 'Navigation@create'),
    array('navigation_edit', 'get', '/backend/navigation/edit/(id:num)', 'Navigation@edit'),
    array('navigation_navitems', 'get', '/backend/navigation/navitems/(id:num)', 'Navigation@navitems'),
    array('navigation_update', 'post', '/backend/navigation/update', 'Navigation@update'),
    array('navigation_delete', 'get', '/backend/navigation/delete/(id:num)', 'Navigation@delete'),
    array('navigation_create_navitem', 'post', '/backend/navigation/create-navitem', 'Navigation@createNavitem'),
    array('navigation_delete_navitem', 'get', '/backend/navigation/delete-navitem/(id:num)', 'Navigation@deleteNavitem'),
    array('navigation_edit_navitem', 'get', '/backend/navigation/edit-navitem/(id:num)', 'Navigation@editNavitem'),
    array('navigation_index', 'get', '/backend/navigations', 'Navigation@index')
));

// Backend navitem routes
$this->addRoutes(array(
    array('navitem_reorder', 'post', '/backend/navitem/reorder', 'Navitem@reorder'),
));

// Backend setting routes
$this->addRoutes(array(
    array('setting_index', 'get', '/backend/settings', 'Setting'),
    array('setting_update', 'post', '/backend/settings/update', 'Setting@update'),
));

// Backend maintenance routes
$this->addRoutes(array(
    array('maintenance_index', 'get', '/backend/maintenance', 'Maintenance@index'),
    array('maintenance_delete_cache', 'post', '/backend/maintenance/delete-cache', 'Maintenance@deleteCache'),
    array('maintenance_create_dump', 'get', '/backend/maintenance/create-dump', 'Maintenance@createDump')
));

// Add frontend route
$this->addRoutes(array(
    array('frontend_error', 'any', false, 'Frontend@error'),
    array('frontend_not_found', 'any', false, 'Frontend@notFound'),
));


