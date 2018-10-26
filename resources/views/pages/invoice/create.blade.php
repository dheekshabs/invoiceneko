@extends("layouts/default")

@section("head")
    <title>{{ config('app.name') }}</title>
    <style>
    </style>
@stop

@section("content")
    <div class="container">
        <div class="row">
            <div class="col s12">
                <h3>Create Invoice</h3>
            </div>
        </div>
        <div class="row">
            <div class="col s12">
                <form id="create-invoice" method="post" enctype="multipart/form-data">
                <div class="card-panel">
                    <div class="row">
                        <div class="input-field col s12">
                            <input id="nice_invoice_id" name="nice_invoice_id" type="text" data-parsley-required="true" data-parsley-trigger="change" data-parsley-minlength="4" data-parsley-pattern="/^[a-zA-Z0-9\-_]{0,40}$/" value="{{ $invoicenumber or '' }}">
                            <label for="nice_invoice_id" class="label-validation">Invoice ID</label>
                        </div>
                    </div>
                    <div class="row">
                        <div class="input-field col s12 m6">
                            <input id="date" name="date" class="datepicker" type="text" data-parsley-required="true" data-parsley-trigger="change" value="{{ old('date') }}" placeholder="Date">
                            <label for="date" class="label-validation">Date</label>
                            <span class="helper-text"></span>
                        </div>
                        <div class="input-field col s12 m6">
                            <input id="netdays" name="netdays" type="number" data-parsley-required="true" data-parsley-trigger="change" value="{{ old('netdays') }}" placeholder="Net Days">
                            <label for="netdays" class="label-validation">Net Days</label>
                            <span class="helper-text"></span>
                        </div>
                    </div>
                    <div class="row">
                        <div class="input-field col s12">
                            <select id="client_id" name="client_id" class="selectize-custom" data-parsley-required="true" data-parsley-trigger="change">
                                <option disabled="" selected="selected" value="">Pick a Client</option>
                                @foreach($clients as $client)
                                    <option value="{{ $client->id }}" @if(old('client_id') == $client->id) selected @endif>{{ $client->companyname }}</option>
                                @endforeach
                            </select>
                            <label for="client_id" class="label-validation">Client</label>
                            <span class="helper-text"></span>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col s6">
                        <h3>Items</h3>
                    </div>
                    <div class="col s6">
                        <a id="invoice-item-add" class="btn-floating btn-large waves-effect waves-light red right"><i class="material-icons">add</i></a>
                    </div>
                </div>
                <div id="invoice-items-container">
                    <div id="invoice_item_0" class="card-panel">
                        <div class="row">
                            <div class="input-field col s8">
                                <select id="item_name_0" name="item_name[]" class="item-list-selector selectize-custom" data-parsley-required="true" data-parsley-trigger="change">
                                    <option disabled="" selected="selected" value="">Pick an Item or Create a new one</option>
                                </select>
                                <label for="item_name_0" class="label-validation">Name</label>
                                <span class="helper-text"></span>
                            </div>
                            <div class="input-field col s2">
                                <input id="item_quantity_0" name="item_quantity[]" class="item-quantity-input" type="number" data-parsley-required="true" data-parsley-trigger="change" value="{{ old('item_quantity') }}" placeholder="Item Quantity">
                                <label for="item_quantity_0" class="label-validation">Quantity</label>
                                <span class="helper-text"></span>
                            </div>
                            <div class="input-field col s2">
                                <input id="item_price_0" name="item_price[]" class="item-price-input" type="number" data-parsley-required="true" data-parsley-trigger="change" value="{{ old('item_price') }}" placeholder="Item Price">
                                <label for="item_price_0" class="label-validation">Price</label>
                                <span class="helper-text"></span>
                            </div>
                            <div class="input-field col s12">
                                <textarea id="item_description_0" name="item_description[]" class="item-description-textarea trumbowyg-textarea" data-parsley-required="true" data-parsley-trigger="change" placeholder="Item Description">{{ old('item_description') }}</textarea>
                                <label for="item_description_0" class="label-validation">Description</label>
                                <span class="helper-text"></span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="input-field col s12">
                        {{ csrf_field() }}
                        <button class="btn waves-effect waves-light col s12 m3 offset-m9" type="submit" name="action">Create</button>
                    </div>
                </div>
                </form>
            </div>
        </div>
    </div>
    <div id="delete-confirmation" class="modal">
        <div class="modal-content">
            <p>Delete Invoice Item?</p>
        </div>
        <div class="modal-footer">
            <a href="javascript:;" class=" modal-action waves-effect black-text waves-green btn-flat btn-deletemodal invoice-item-confirm-delete-btn">Delete</a>
            <a href="javascript:;" class=" modal-action modal-close waves-effect black-text waves-red btn-flat btn-deletemodal">Cancel</a>
        </div>
    </div>
@stop

@section("scripts")
    <script type="text/javascript">
        "use strict";
        $(function() {
            let invoiceitemcount = 0;

            $('.trumbowyg-textarea').trumbowyg({
                svgPath: '/assets/fonts/trumbowygicons.svg',
                removeformatPasted: true,
                resetCss: true,
                autogrow: true,
            });

            $('.datepicker').datepicker({
                autoClose: 'false',
                format: 'd mmmm, yyyy',
                yearRange: [1950, 2018],
                onSelect: function() {
                    // let date = $(this)[0].formats.yyyy() + '-' + $(this)[0].formats.mm() + '-' + $(this)[0].formats.dd()
                    // $('#receiveddate').val(date);
                }
            });

            $('#client_id').selectize();
            initElements();

            $('#invoice-item-add').on('click', function() {
                initInvoiceItem(++invoiceitemcount, 'invoice-items-container');
            });

            function initElements()
            {
                $('.trumbowyg-textarea').trumbowyg({
                    svgPath: '/assets/fonts/trumbowygicons.svg',
                    removeformatPasted: true,
                    resetCss: true,
                    autogrow: true,
                });

                //Explicit selection otherwise will change the select into a multiple select if only selecting by css class
                $('select.item-list-selector').selectize({
                    create: false,
                    dataAttr: 'data-id',
                    valueField: 'name',
                    labelField: 'name',
                    searchField: ['name'],
                    options: [
                            @foreach($itemtemplates as $itemtemplate){ id:'{{ $itemtemplate->id }}', name:'{{ $itemtemplate->name }}' },@endforeach
                    ],
                    render: {
                        option: function(data) {
                            return '<div class="option" data-selectable="" data-id="' + data.id +'" data-value="' + data.name +'">' + data.name +'</div>';
                        }
                    }
                });
            }

            function initInvoiceItem(count, elementid) {
                let invoiceitem = '<div id="invoice_item_' + count + '" class="card-panel"><div class="row"><div class="input-field col s8"> <select id="item_name_' + count + '" name="item_name[]" class="item-list-selector selectize-custom" data-parsley-required="true" data-parsley-trigger="change"><option disabled="" selected="selected" value="">Pick an Item or Create a new one</option> </select> <label for="item_name_' + count + '" class="label-validation">Name</label> <span class="helper-text"></span></div><div class="input-field col s2"> <input id="item_quantity_' + count + '" name="item_quantity[]" class="item-quantity-input" type="number" data-parsley-required="true" data-parsley-trigger="change" placeholder="Item Quantity"> <label for="item_quantity_' + count + '" class="label-validation">Quantity</label> <span class="helper-text"></span></div><div class="input-field col s2"> <input id="item_price_' + count + '" name="item_price[]" class="item-price-input" type="number" data-parsley-required="true" data-parsley-trigger="change" placeholder="Item Price"> <label for="item_price_' + count + '" class="label-validation">Price</label> <span class="helper-text"></span></div><div class="input-field col s12"><textarea id="item_description_' + count + '" name="item_description[]" class="item-description-textarea trumbowyg-textarea" data-parsley-required="true" data-parsley-trigger="change" placeholder="Item Description"></textarea><label for="item_description_' + count + '" class="label-validation">Description</label> <span class="helper-text"></span></div></div><div class="row"> <button data-id="false" data-count="' + count + '" class="invoice-item-delete-btn btn waves-effect waves-light col s12 m3 offset-m9 red">Delete</button></div></div>';
                $('#' + elementid).append(invoiceitem);
                initElements();
            }

            function retrieveItemTemplate(itemtemplate_id, element, callback) {
                if (typeof itemtemplate_id !== typeof undefined && itemtemplate_id !== false) {
                    $.ajax({
                        type: "GET",
                        url: "/itemtemplate/" + itemtemplate_id +"/retrieve",
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        }
                    })
                        .done(function(data) {
                            callback(element, data);
                        })
                        .fail(function(jqXHR, textStatus) {
                            console.log(jqXHR);
                            console.log(textStatus);
                        })
                        .always(function() {
                            console.log("finish");
                        });
                }
            }

            function setItemTemplate(element, itemtemplate) {
                element.val(itemtemplate.name);
                let quantityElement = element.parentsUntil(".card-panel").find('.item-quantity-input');
                let priceElement = element.parentsUntil(".card-panel").find('.item-price-input');
                let descriptionElement = element.parentsUntil(".card-panel").find('.item-description-textarea');

                quantityElement.val(itemtemplate.quantity);
                priceElement.val(itemtemplate.price);
                descriptionElement.trumbowyg('html', itemtemplate.description);
            }

            $('#invoice-items-container').on('click', '.invoice-item-delete-btn', function (event) {
                event.preventDefault();
                $('#delete-confirmation').modal('open');

                let itemid = $(this).attr('data-id');
                let count = $(this).attr('data-count');

                $('#delete-confirmation').children().children('.invoice-item-confirm-delete-btn').attr('data-id', itemid);
                $('#delete-confirmation').children().children('.invoice-item-confirm-delete-btn').attr('data-count', count);
            });

            $('#invoice-items-container').on('change', '.item-list-selector', function (event) {
                retrieveItemTemplate($(this).siblings().find('.selected').attr('data-id'), $(this), setItemTemplate);
            });

            $('#delete-confirmation').on('click', '.invoice-item-confirm-delete-btn', function (event) {
                event.preventDefault();

                let itemid = $(this).attr('data-id');
                let count = $(this).attr('data-count');

                if (itemid == "false") {
                    $('#invoice_item_' + count).remove();
                    $('#delete-confirmation').modal('close');
                }
            });

            $('#create-invoice').parsley({
                successClass: 'valid',
                errorClass: 'invalid',
                errorsContainer: function (velem) {
                    let $errelem = velem.$element.siblings('span.helper-text');
                    $errelem.attr('data-error', window.Parsley.getErrorMessage(velem.validationResult[0].assert));
                    return true;
                },
                errorsWrapper: '',
                errorTemplate: ''
            })
                .on('field:validated', function(velem) {

                })
                .on('field:success', function(velem) {
                    if (velem.$element.is('select')) {
                        velem.$element.siblings('.selectize-control').removeClass('invalid').addClass('valid');
                        //velem.$element.parent('.select-wrapper').removeClass('invalid').addClass('valid');
                        //velem.$element.siblings('.select-dropdown').removeClass('invalid').addClass('valid');
                    }
                })
                .on('field:error', function(velem) {
                    if (velem.$element.is('select')) {
                        velem.$element.siblings('.selectize-control').removeClass('valid').addClass('invalid');

                        //velem.$element.parent('.select-wrapper').removeClass('valid').addClass('invalid');
                        //velem.$element.siblings('.select-dropdown').removeClass('valid').addClass('invalid');
                        //velem.$element.parent('.select-wrapper').siblings('label').attr('data-error', window.Parsley.getErrorMessage(velem.validationResult[0].assert));
                    }
                })
                .on('form:submit', function(velem) {

                });
        });
    </script>
@stop