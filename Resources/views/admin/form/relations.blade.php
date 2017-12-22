
@php
    $relations = config('netcore.module-invoice.relations');
    $belongsTo = collect($relations)->where('type', 'belongsTo');
@endphp

@foreach($belongsTo as $relation)

    @php
        $foreignKey = array_get($relation, 'foreignKey');
        $name = array_get($relation, 'name');

        $selected = null;
        $options = [];

        $relationalObject = $model->$name;

        if($relationalObject) {

            $repo = new \Modules\Invoice\Repositories\InvoiceRepository();
            $label = $repo->labelForRelationItem($relationalObject, $foreignKey);

            $options = [
                $relationalObject->id => $label
            ];

            $selected = $relationalObject->id;
        }
    @endphp

    <fieldset class="form-group form-group-lg {{ $errors->has($foreignKey) ? 'form-message-light has-error has-validation-error' : '' }}">
        <label for="invoice_nr">{{ ucfirst($name) }}</label>

        {{ Form::select($foreignKey, $options, $selected, [
            'class' => 'invoice-relation-select',
            'data-url' => route('invoice::relation-pagination'),
            'data-foreign-key' => $foreignKey
        ]) }}

        @if ($errors->has($foreignKey))
            <div id="validation-message-light-error" class="form-message validation-error">
                @foreach ($errors->get($foreignKey) as $message)
                    {{ $message }} <br>
                @endforeach
            </div>
        @endif
    </fieldset>
@endforeach

