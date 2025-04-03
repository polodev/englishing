<!-- Alpine.js -->
<script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

<script src="{{ asset('vendor/flatpickr/flatpickr.min.js') }}"></script>
<script src="{{ asset('vendor/datatables/js/jquery.dataTables.min.js') }}"></script>
<script src="https://cdn.datatables.net/buttons/1.7.0/js/dataTables.buttons.min.js"></script>
<script src="https://cdn.datatables.net/buttons/1.7.0/js/buttons.html5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/1.7.0/js/buttons.print.min.js"></script>
<script src="https://cdn.datatables.net/fixedheader/3.2.3/js/dataTables.fixedHeader.min.js"></script>



<!-- Custom Scripts -->
<script>
    document.addEventListener('alpine:init', () => {
        // Initialize any Alpine.js data or components here
        Alpine.data('notifications', () => ({
            open: false,
            toggle() {
                this.open = !this.open;
            }
        }));

        Alpine.data('dropdown', () => ({
            open: false,
            toggle() {
                this.open = !this.open;
            },
            close() {
                this.open = false;
            }
        }));
    });




</script>

<script>
    // for datatable pageNumber navigate
    function dataTableNavigate(dataTableName) {
      var params_for_datatable_page_number = new URLSearchParams(window.location.search)
      var dt_page_number = parseInt(params_for_datatable_page_number.get('page'))
      if (dt_page_number) {
        dt_page_number--
        dataTableName.on('init.dt', function(e) {
          dataTableName.page(dt_page_number).draw(false);
        });
      }

      const searchURL = new URL(window.location);
      dataTableName.on('draw.dt', function() {
        var info = dataTableName.page.info();
        searchURL.searchParams.set('page', (info.page + 1));
        window.history.replaceState({}, '', searchURL);
      });

      // Go to Datatable page
      $('#datatable-page-number-button').on('click', function() {
        var page_number = parseInt($('#datatable-page-number').val());
        if (page_number) {
          page_number--
          dataTableName.page(page_number).draw(false);
        }
      })
    } // ending dataTableNavigate fn
</script>