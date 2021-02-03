<!-- BEGIN: Head-->

<head>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0">
	<meta name="description" content="@yield('description')">
	<meta name="keywords" content="">
	<meta name="author" content="GSF">
	<title>@yield('title')</title>
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.2.0/jquery.min.js"></script>
	<link rel="apple-touch-icon" href="{{ asset('frontend/img/logo.png') }}">
	<link rel="shortcut icon" type="image/x-icon" href="{{ asset('frontend/img/logo.png') }}">
	<link href="https://fonts.googleapis.com/css?family=Rubik:300,400,500,600%7CIBM+Plex+Sans:300,400,500,600,700"
		rel="stylesheet">

	<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.min.js"></script>


	<!--Font Awesome--->
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">

	<!--Include CK Editor-->
	<script src="https://cdn.ckeditor.com/4.14.0/full/ckeditor.js"></script>

	<!-- BEGIN: Vendor CSS-->
	<link rel="stylesheet" type="text/css" href="{{ asset('app-assets/vendors/css/vendors.min.css') }}">
	{{-- <link rel="stylesheet" type="text/css" href="{{ asset('app-assets/vendors/css/editors/quill/katex.min.css') }}">
	<link rel="stylesheet" type="text/css"
		href="{{ asset('app-assets/vendors/css/editors/quill/monokai-sublime.min.css') }}">
	<link rel="stylesheet" type="text/css" href="{{ asset('app-assets/vendors/css/editors/quill/quill.snow.css') }}">
	<link rel="stylesheet" type="text/css" href="{{ asset('app-assets/vendors/css/editors/quill/quill.bubble.css') }}">
	--}}
	<link rel="stylesheet" type="text/css" href="{{ asset('app-assets/vendors/css/ui/prism.min.css') }}">

	<!-- END: Vendor CSS-->

	<!-- BEGIN: Theme CSS-->
	<link rel="stylesheet" type="text/css" href="{{ asset('app-assets/css/bootstrap.css') }}">
	<link rel="stylesheet" type="text/css" href="{{ asset('app-assets/css/bootstrap-extended.css') }}">
	<link rel="stylesheet" type="text/css" href="{{ asset('app-assets/css/colors.css') }}">
	<link rel="stylesheet" type="text/css" href="{{ asset('app-assets/css/components.css') }}">
	<link rel="stylesheet" type="text/css" href="{{ asset('app-assets/css/themes/dark-layout.css') }}">
	<link rel="stylesheet" type="text/css" href="{{ asset('app-assets/css/themes/semi-dark-layout.css') }}">
	<!-- END: Theme CSS-->

	<!-- BEGIN: Page CSS-->
	<link rel="stylesheet" type="text/css" href="{{ asset('app-assets/css/core/menu/menu-types/vertical-menu.css') }}">
	<link rel="stylesheet" type="text/css" href="{{ asset('app-assets/css/pages/authentication.css') }}">
	<link rel="stylesheet" type="text/css"
		href="{{ asset('app-assets/vendors/css/tables/datatable/datatables.min.css') }}">
	<link rel="stylesheet" type="text/css" href="{{ asset('app-assets/css/core/menu/menu-types/vertical-menu.css')}}">


	<!-- END: Page CSS-->
	
	<!-- BEGIN: Custom CSS-->
	<link rel="stylesheet" type="text/css" href="{{ asset('assets/css/style.css') }}">
	<script src="http://malsup.github.com/jquery.form.js"></script>
	<!-- END: Custom CSS-->
	<!--Dropzone Links-->
	{{-- <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.2.0/jquery.min.js"></script> --}}
	
	<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />

	<!---Modal CSS-->
	<style>
		.vertical-alignment-helper {
			display: table;
			height: 100%;
			width: 100%;
			pointer-events: none;
		}

		.vertical-align-center {
			/* To center vertically */
			display: table-cell;
			vertical-align: middle;
			pointer-events: none;
		}

		.modal-content {
			/* Bootstrap sets the size of the modal in the modal-dialog class, we need to inherit it */
			width: inherit;
			max-width: inherit;
			/* For Bootstrap 4 - to avoid the modal window stretching 
    full width */
			height: inherit;
			/* To center horizontally */
			margin: 0 auto;
			pointer-events: all;
		}
	</style>
	@yield('extra_styles')
</head>
<!-- END: Head-->