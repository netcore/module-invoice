<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.6-rc.0/js/select2.min.js"></script>

<script>

    var formatRepo = function (repo) {
        return repo.text;
    };

    var initSelect2 = function(){
        $('.invoice-relation-select').each(function(i, o){
            $(o).select2({
                ajax: {
                    url: $(o).data('url'),
                    dataType: 'json',
                    delay: 250,
                    data: function (params) {
                        return {
                            q: params.term, // search term
                            page: params.page || 1,
                            foreignKey: $(o).data('foreign-key')
                        };
                    },
                    processResults: function (data, params) {
                        params.page = params.page || 1;
                        return {
                            results: data.items,
                            pagination: {
                                more: (params.page * 30) < data.total_count
                            }
                        };
                    },
                    cache: false
                },
                escapeMarkup: function (markup) { return markup; }, // let our custom formatter work
                minimumInputLength: 0,
                templateResult: formatRepo
            });
        });
    };

    var replaceAll = function(search, replacement, source) {
        return source.split(search).join(replacement);
    };

    var randomString = function() {
        var text = "";
        var possible = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789";

        for (var i = 0; i < 5; i++)
            text += possible.charAt(Math.floor(Math.random() * possible.length));

        return text;
    };

    var addEmptyItemOnPageload = function(){
        var count = $('.invoice-item-tr').length;
        if(!count){
            $('#add-invoice-item').click();
        }
    };

    var showForm = function(){
        $('#form-loading').hide();
        $('#form-body').show();
        console.log('showing');
    };

    initSelect2();

    $('body').on('click', '#add-invoice-item', function(){
        var itemId = randomString();
        var html = $('#invoice-item-template').html();

        html = replaceAll('@{{ itemId }}', itemId, html);
        $('#invoice-items-table tr:last').before(html);
    });

    $('body').on('click', '#add-invoice-shipping-item', function(){
        var itemId = randomString();
        var html = $('#invoice-shipping-item-template').html();
        html = replaceAll('@{{ itemId }}', itemId, html);
        $('#invoice-items-table tr:last').before(html);
    });

    var calculateTotals = function() {
        var priceWithoutVat = 0;
        var priceWithVat = 0;

        $('.invoice-item-tr').each(function(index, tr) {

            var trWithoutVat = parseFloat(
                $(tr).find('.calculations-price-without-vat').val()
            ) || 0;

            var trWithVat = parseFloat(
                $(tr).find('.calculations-price-with-vat').val()
            ) || 0;

            var trQuantity = parseFloat(
                $(tr).find('.calculations-quantity').val()
            ) || 0;

            priceWithoutVat += (trWithoutVat * trQuantity);
            priceWithVat += (trWithVat * trQuantity);
        });

        priceWithoutVat = priceWithoutVat.toFixed(2);
        priceWithVat = priceWithVat.toFixed(2);

        $('#total-without-vat').val(priceWithoutVat);
        $('#total-with-vat').val(priceWithVat);
    };

    var calculateOppositeValue = function(e) {
        var $input = $(e.target);
        var isWithVat = $input.hasClass('calculations-price-with-vat');
        var vat = parseInt($('input#vat').val());

        if (isNaN(vat)) {
            return;
        }

        var vatFull = 1 + (vat * 0.01);

        var oppositeClass = isWithVat ? 'calculations-price-without-vat' : 'calculations-price-with-vat';
        var $oppositeInput = $input.parent().parent().find('input.' + oppositeClass);
        var value;

        var inputValue = parseFloat($input.val());

        if (isNaN(inputValue)) {
            return;
        }

        if (isWithVat) {
            value = parseFloat($input.val()) / vatFull;
        } else {
            value = parseFloat($input.val()) * vatFull;
        }

        $oppositeInput.val(value.toFixed(2));
    };

    var keyupClasses = '.calculations-price-without-vat';
        keyupClasses += ', .calculations-price-with-vat';
        keyupClasses += ', .calculations-quantity';

    $('body').on('keyup change', keyupClasses, function(e) {
        calculateTotals();
        calculateOppositeValue(e);
    });

    $('body').on('click', '.delete-invoice-item', function (e) {
        e.preventDefault();
        var btn = $(this);

        swal({
            title: $(btn).data('title') || 'Confirmation',
            text: $(btn).data('text') || 'Are you sure?',
            type: "warning",
            showCancelButton: true,
            confirmButtonColor: "#DD6B55",
            confirmButtonText: $(btn).data('confirm-button-text') || 'Confirm',
            cancelButtonText: $(btn).data('cancel-button-text') || 'Cancel'
        }).then(function(){
            var fadeOutSelector = btn.data('fade-out-selector');
            if ( fadeOutSelector && $(fadeOutSelector).length ) {
                $(fadeOutSelector).fadeOut(function(){
                    $(fadeOutSelector).remove();
                    calculateTotals();
                });
            }
        }).catch(swal.noop);
    });

    showForm();
    addEmptyItemOnPageload();

    // Calculate item after vat change.
    $('input#vat').on('change keyup', function() {
        var $input = $(this);
        var value = parseInt($input.val());

        if (isNaN(value)) {
            return;
        }

        $('.invoice-item-tr').each(function(index, tr) {
            var $inputWithoutVat = $(tr).find('.calculations-price-without-vat');
            var $inputWithVat = $(tr).find('.calculations-price-with-vat');

            var priceWithoutVat = parseFloat($inputWithoutVat.val()) || 0;
            var priceWithVat = priceWithoutVat * (1 + value * 0.01);

            $inputWithVat.val(priceWithVat.toFixed(2));
        });

        calculateTotals();
    });
</script>

<script>
    $(function () {
        $('.datepicker').datepicker({
            format: 'yyyy-mm-dd'
        });

        $('textarea').summernote({
            height: 200,
            toolbar: [
                ['parastyle', ['style']],
                ['fontstyle', ['fontname', 'fontsize']],
                ['style', ['bold', 'italic', 'underline', 'clear']],
                ['font', ['strikethrough', 'superscript', 'subscript']],
                ['color', ['color']],
                ['para', ['ul', 'ol', 'paragraph']],
                ['height', ['height']],
                ['insert', ['picture', 'link', 'video', 'table', 'hr']],
                ['history', ['undo', 'redo']],
                ['misc', ['codeview', 'fullscreen']]
            ]
        });
    });
</script>
