@include('admin.production.header')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.css"/>

<div class="right_col" role="main">
    <div class="row">
        <div class="col-md-12">
            <h3>Searched Keys Of Partner</h3>

            <div class="col-xs-12">
                <div class="table-responsive">
                    <table id="searchKeysWithoutResultList" class="table table-bordered table-hover table-striped projects">
                        <thead>
                            <tr>
                                <th>Keys</th>
                                <th>Search Counts</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($keys as $value)
                                <tr>
                                    <td>{{$value->key}}</td>
                                    <td>{{$value->search_key_count}}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

@include('admin.production.footer')
<script src="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.js"></script>

