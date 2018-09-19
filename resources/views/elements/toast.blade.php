@section('css')
  <link rel="stylesheet" href="/inspinia/css/plugins/toastr/toastr.min.css"/>
  @parent
@endsection

@section('js-footer')
  @parent
  <script src="/inspinia/js/toastr/toastr.min.js"></script>
  <script type="text/javascript">
    (function () {
      'use strict';

      var type = '{{ Session::get('toast.type', '') }}';
      var message = '{{ Session::get('toast.message', '') }}';
      var title = '{{ Session::get('toast.title', '') }}';

      $(document).ready(function () {
        initToastrOption(toastr);

        if (message.length < 1) {
          return;
        }

        if (type === 'success') {
          toastr.success(message, title);
        } else if (type === 'info') {
          toastr.info(message, title);
        } else if (type === 'warning') {
          toastr.warning(message, title);
        } else if (type === 'error') {
          toastr.error(message, title);
        }
      });

      function initToastrOption(toastr) {
        toastr.options = {
          "closeButton": true,
          "debug": false,
          "progressBar": false,
          "positionClass": "toast-top-right",
          "onclick": null,
          "showDuration": "400",
          "hideDuration": "400",
          "timeOut": "6000",
          "extendedTimeOut": "1400",
          "showEasing": "swing",
          "hideEasing": "linear",
          "showMethod": "fadeIn",
          "hideMethod": "fadeOut"
        }
      }

    })();
  </script>
@endsection
