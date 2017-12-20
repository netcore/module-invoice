@extends('crud::layouts.master')

@section('crudName', 'Edit')

@section('crudPanelName', $model->getClassName() . ' ' . $model->invoice_nr)

@section('crud')
    @include('admin::_partials._messages')

    {!! Form::model($model, ['url' => crud_route('update', $model->id)]) !!}
    {{ method_field('PUT') }}
    <div class="p-x-1">

        @include('invoice::admin.form.relations')
        @include('invoice::admin.form.base')

        <div class="row">
            <div class="col-md-6">
                @include('invoice::admin.form.sender')
            </div>
            <div class="col-md-6">
                @include('invoice::admin.form.receiver')
            </div>
        </div>

        <div class="row">
            <div class="col-md-12">
                @include('invoice::admin.form.items')
            </div>
        </div>

        <button type="submit" class="btn btn-md btn-success m-t-3 pull-xs-right">
            <i class="fa fa-save"></i> Save
        </button>

        <a href="{{ crud_route('index') }}" class="btn btn-md btn-default m-t-3 m-r-1 pull-xs-right">
            <i class="fa fa-undo"></i> Back
        </a>
    </div>
    {!! Form::close() !!}
@endsection

@section('scripts')

    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.6-rc.0/js/select2.min.js"></script>

    <script>

        function formatRepo (repo) {

            return repo.text;
            /*
            console.log(repo);
            return repo.title;

            if (repo.loading) {
                return repo.text;
            }

            var markup = "<div class='select2-result-repository clearfix'>" +
                "<div class='select2-result-repository__avatar'><img src='" + repo.owner.avatar_url + "' /></div>" +
                "<div class='select2-result-repository__meta'>" +
                "<div class='select2-result-repository__title'>" + repo.full_name + "</div>";

            if (repo.description) {
                markup += "<div class='select2-result-repository__description'>" + repo.description + "</div>";
            }

            markup += "<div class='select2-result-repository__statistics'>" +
                "<div class='select2-result-repository__forks'><i class='fa fa-flash'></i> " + repo.forks_count + " Forks</div>" +
                "<div class='select2-result-repository__stargazers'><i class='fa fa-star'></i> " + repo.stargazers_count + " Stars</div>" +
                "<div class='select2-result-repository__watchers'><i class='fa fa-eye'></i> " + repo.watchers_count + " Watchers</div>" +
                "</div>" +
                "</div></div>";

            return markup;
            */
        }

        function formatRepoSelection (repo) {
            return repo.text;
        }

        $('.invoice-relation-select').each(function(i, o){
            $(o).select2({
                ajax: {
                    url: $(o).data('url'),
                    dataType: 'json',
                    // Additional AJAX parameters go here; see the end of this chapter for the full code of this example
                    delay: 250,
                    data: function (params) {
                        return {
                            q: params.term, // search term
                            page: params.page || 1,
                            foreignKey: $(o).data('foreign-key')
                        };
                    },
                    processResults: function (data, params) {
                        // parse the results into the format expected by Select2
                        // since we are using custom formatting functions we do not need to
                        // alter the remote JSON data, except to indicate that infinite
                        // scrolling can be used
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
                placeholder: 'Search for a repository',
                escapeMarkup: function (markup) { return markup; }, // let our custom formatter work
                minimumInputLength: 1,
                templateResult: formatRepo,
                //templateSelection: formatRepoSelection
            });
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
                ],
            });
        });
    </script>
@endsection

@section('styles')

    <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.6-rc.0/css/select2.min.css" rel="stylesheet" />

    <style>
        #invoice-items-table,
        #invoice-items-table table
        {
            width: 100%;
        }

        #invoice-items-table td,
        #invoice-items-table th
        {
            padding:5px;
        }
    </style>
@endsection
