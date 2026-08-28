<footer class="footer footer-static footer-light">
	<p class="clearfix mb-0"><span class="float-left d-inline-block"></span><span
			class="float-right d-sm-inline-block d-none">&copy; {{ date('Y') }} {{ strtoupper($edition->ministry->code ?? env('APP_NAME'))}} | Built with love by<a class="text-uppercase"
				href="https://dembrix.com/" target="_blank">Dembrix</a></span>
		<button class="btn btn-primary btn-icon scroll-top" type="button"><i class="fa fa-arrow-up" aria-hidden="true"></i>
		</button>
	</p>
</footer>
<!-- BEGIN: Vendor JS-->
<script src="{{ asset('app-assets/vendors/js/vendors.min.js') }}"></script>

<script src="{{ asset('app-assets/fonts/LivIconsEvo/js/LivIconsEvo.tools.js') }}"></script>
<script src="{{ asset('app-assets/fonts/LivIconsEvo/js/LivIconsEvo.defaults.js') }}"></script>
<script src="{{ asset('app-assets/fonts/LivIconsEvo/js/LivIconsEvo.min.js') }}"></script>
<!-- BEGIN Vendor JS-->

<!-- BEGIN: Page Vendor JS-->
<script src="{{ asset('app-assets/vendors/js/charts/apexcharts.min.js') }}"></script>
<script src="{{ asset('app-assets/vendors/js/extensions/swiper.min.js') }}"></script>
<script src="{{ asset('app-assets/vendors/js/tables/datatable/datatables.min.js') }}"></script>
<script src="{{ asset('app-assets/vendors/js/tables/datatable/dataTables.bootstrap4.min.js') }}"></script>
<script src="{{ asset('app-assets/vendors/js/tables/datatable/dataTables.buttons.min.js') }}"></script>
<script src="{{ asset('app-assets/vendors/js/tables/datatable/buttons.html5.min.js') }}"></script>
<script src="{{ asset('app-assets/vendors/js/tables/datatable/buttons.print.min.js') }}"></script>
<script src="{{ asset('app-assets/vendors/js/tables/datatable/buttons.bootstrap.min.js') }}"></script>
<script src="{{ asset('app-assets/vendors/js/tables/datatable/pdfmake.min.js') }}"></script>
<script src="{{ asset('app-assets/vendors/js/tables/datatable/vfs_fonts.js') }}"></script>

<script src="{{ asset('app-assets/vendors/js/extensions/jquery.steps.min.js') }}"></script>
<script src="{{ asset('app-assets/vendors/js/forms/validation/jquery.validate.min.js') }}"></script>

<!-- END: Page Vendor JS-->

<!-- BEGIN: Theme JS-->
<script src="{{ asset('app-assets/js/scripts/configs/vertical-menu-light.js') }}"></script>
<script src="{{ asset('app-assets/js/core/app-menu.js') }}"></script>
<script src="{{ asset('app-assets/js/core/app.js') }}"></script>
<script src="{{ asset('app-assets/js/scripts/components.js') }}"></script>
<script src="{{ asset('app-assets/js/scripts/footer.js') }}"></script>
<!-- END: Theme JS-->

<!-- BEGIN: Page JS-->
<script src="{{ asset('app-assets/js/scripts/pages/dashboard-ecommerce.js') }}"></script>
<script src="{{ asset('app-assets/js/scripts/datatables/datatable.js') }}"></script>

<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
// $(document).ready(function() {



// });
$(document).ready(function () {
    $('select').select2({
	    width: '100%' // need to override the changed default
	});

	$("#role").change(function(){
		if(this.value == 1 || this.value == 2){
			$("#session").hide();
		}else{
			$("#session").show();
		}

	});
    const selector = '.zero-configuration';

    if ($.fn.DataTable.isDataTable(selector)) {
        $(selector).DataTable().destroy();
    }

    $(selector).DataTable({
        pageLength: 20,
        lengthMenu: [[20, 30, 50, 100, -1], [20, 30, 50, 100, "All"]],
        ordering: true,
        searching: true,
        responsive: true,
        autoWidth: false,
    });
});


</script>
@yield('extra_scripts')
{{-- <script src="{{ asset('app-assets/js/scripts/editors/editor-quill.js') }}"></script> --}}
<!-- END: Page JS-->
<!-- END: Footer-->
