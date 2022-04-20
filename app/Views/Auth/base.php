<!DOCTYPE html>
<html lang="en">
	<head>

		<meta charset="utf-8">
		<meta content="width=device-width, initial-scale=1, shrink-to-fit=no" name="viewport">
		<meta name="description" content="Dashlead -  Admin Panel HTML Dashboard Template">
		<meta name="author" content="Spruko Technologies Private Limited">
		<meta name="keywords" content="sales dashboard, admin dashboard, bootstrap 4 admin template, html admin template, admin panel design, admin panel design, bootstrap 4 dashboard, admin panel template, html dashboard template, bootstrap admin panel, sales dashboard design, best sales dashboards, sales performance dashboard, html5 template, dashboard template">

		<!-- Favicon -->
		<link rel="icon" href="<?= ASSETS ?>/img/brand/favicon.ico" type="image/x-icon"/>

		<!-- Title -->
		<title><?= TITLE;?></title>

		<!---Fontawesome css-->
		<link href="<?= ASSETS ?>/plugins/fontawesome-free/css/all.min.css" rel="stylesheet">

		<!---Ionicons css-->
		<link href="<?= ASSETS ?>/plugins/ionicons/css/ionicons.min.css" rel="stylesheet">

		<!---Typicons css-->
		<link href="<?= ASSETS ?>/plugins/typicons.font/typicons.css" rel="stylesheet">

		<!---Feather css-->
		<link href="<?= ASSETS ?>/plugins/feather/feather.css" rel="stylesheet">

		<!---Falg-icons css-->
		<link href="<?= ASSETS ?>/plugins/flag-icon-css/css/flag-icon.min.css" rel="stylesheet">

		<!---DataTables css-->
		<link href="<?= ASSETS ?>/plugins/datatable/dataTables.bootstrap4.min.css" rel="stylesheet" />
		<link href="<?= ASSETS ?>/plugins/datatable/responsivebootstrap4.min.css" rel="stylesheet" />
		<link href="<?= ASSETS ?>/plugins/datatable/fileexport/buttons.bootstrap4.min.css" rel="stylesheet" />

		<!---Select2 css-->
		<link href="<?= ASSETS ?>/plugins/select2/css/select2.min.css" rel="stylesheet">

		<!---Style css-->
		<link href="<?= ASSETS ?>/css/style.css" rel="stylesheet">
		<link href="<?= ASSETS ?>/css/custom-style.css" rel="stylesheet">
		<link href="<?= ASSETS ?>/css/skins.css" rel="stylesheet">
		<link href="<?= ASSETS ?>/css/dark-style.css" rel="stylesheet">
		<link href="<?= ASSETS ?>/css/custom-dark-style.css" rel="stylesheet">

		<!---Sidebar css-->
		<link href="<?= ASSETS ?>/plugins/sidebar/sidebar.css" rel="stylesheet">

		<!---Sidemenu css-->
		<link href="<?= ASSETS ?>/plugins/sidemenu/closed-sidemenu.css" rel="stylesheet">

	</head>

	<body class="main-body">

		<!-- Loader -->
		<div id="global-loader">
			<img src="<?= ASSETS ?>/img/loader.svg" class="loader-img" alt="Loader">
		</div>
		<!-- End Loader -->

		<?= $this->renderSection('content') ?>

        <?= $this->include(THEME . 'component/scripts') ?>
        <?= $this->renderSection('scripts') ?>
	</body>
</html>