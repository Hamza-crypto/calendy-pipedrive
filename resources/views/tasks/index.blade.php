@extends('layouts.app')

@section('title', 'Tasks')

@section('scripts')
    <script>

        $(document).ready(function () {

            $("input[id=\"daterange\"]").daterangepicker({

                autoUpdateInput: false,
            }).on('apply.daterangepicker', function (ev, picker) {
                $(this).val(picker.startDate.format('MM/DD/YYYY') + ' - ' + picker.endDate.format('MM/DD/YYYY'));
            }).on('cancel.daterangepicker', function (ev, picker) {
                $(this).val('');
            });

            var table = $('#tasks-table').DataTable({
                "ordering": true,
                'processing': true,
                'serverSide': true,
                'ajax': {
                    'url': "{{  route('tasks.ajax')  }}",
                    "dataType": "json",
                    "type": "GET",
                    "data": function (data) {

                        data.stage = $('#stage').val();
                        data.sms_status = $('#sms_status').val();
                        data.daterange = $('#daterange').val();

                        var queryString = 'search=' + data.search.value + '&stage=' + data.stage + '&sms_status=' + data.sms_status + '&daterange=' + data.daterange;
                        var newurl = window.location.protocol + "//" + window.location.host + window.location.pathname + '?' + queryString;
                        window.history.pushState({path: newurl}, '', newurl);

                    },
                    dataSrc: function (data) {
                        return data.data;
                    }
                },
                'columns': [
                    {"data": "id"},
                    {"data": "email"},
                    {"data": "phone"},
                    {"data": "stage"},
                    {"data": "sms_id"},
                    {"data": "sms_status"},
                    {"data": "vm_status"},
                    {"data": "vm_reason"},
                    {"data": "status"},
                    {"data": "created_at2"}

                ],
                columnDefs: [
                    {
                        targets: [1, 2, 3, 4, 5, 6, 7, 8, 9], orderable: false
                    },

                ],

            });

            $('.apply-dt-filters').on('click', function () {
                table.ajax.reload();
            });

            $('.clear-dt-filters').on('click', function () {
                $('#stage').val('-100').trigger('change');
                $('#sms_status').val('-100').trigger('change');
                $('#daterange').val('');

                table.search("");
                table.ajax.reload();
            });

        });
    </script>
@endsection

@section('content')

    <h1 class="h3 mb-3">All Tasks</h1>

    {{--    @include('pages.order._inc.stats')--}}

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <form>
                        <input type="hidden" class="d-none" name="filter" value="true" hidden>
                        <div class="row">
                            <div class="col-sm">
                                <div class="form-group">
                                    <label class="form-label" for="stage"> Stage </label>
                                    <select name="stage" id="stage"
                                            class="form-control form-select custom-select select2"
                                            data-toggle="select2">
                                        <option value="-100"> Select Stage</option>
                                        @foreach($stages as $stage)
                                            <option
                                                value="{{ $stage->name }}" {{ request()->stage == $stage->name ? 'selected' : '' }}> {{ $stage->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>


                            <div class="col-sm">
                                <div class="form-group">
                                    <label class="form-label" for="sms_status"> SMS Status </label>
                                    <select name="sms_status" id="sms_status"
                                            class="form-control form-select custom-select select2"
                                            data-toggle="select2">
                                        <option value="-100"> Select Status</option>
                                        <option value="queued"> Queued</option>
                                        <option value="sent"> Sent</option>
                                        <option value="delivered"> Delivered</option>

                                    </select>
                                </div>
                            </div>

                            <div class="col-sm">
                                <div class="form-group">
                                    <label class="form-label" for="daterange">{{ __('Date Range') }}</label>
                                    <input id="daterange" class="form-control" type="text" name="daterange"
                                           value="{{ request()->daterange }}"
                                           placeholder="{{ __('Select Date range') }}"/>
                                </div>
                            </div>

                        </div>

                        <div class="row">
                            <div class="col-sm mt-4">
                                <button type="button"
                                        class="btn btn-sm btn-primary apply-dt-filters mt-2">{{ __('Apply') }}</button>
                                <button type="button"
                                        class="btn btn-sm btn-secondary clear-dt-filters mt-2">{{ __('Clear') }}</button>

                                {{--                                <button type="button" class="btn btn-sm btn-secondary mt-2"--}}
                                {{--                                        onclick="get_query_params2()"--}}
                                {{--                                >{{ 'Export ' }}</button>--}}

                            </div>
                        </div>


                    </form>

                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">

                    <table id="tasks-table" class="table table-striped" style="width:100%">
                        <thead>
                        <tr>
                            <th>ID</th>
                            <th>Email</th>
                            <th>Phone</th>
                            <th>Stage</th>
                            <th>Twilio ID</th>
                            <th>SMS Status</th>
                            <th>VM Status</th>
                            <th>Reason</th>
                            <th>Status</th>
                            <th>Created at</th>
                        </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection


