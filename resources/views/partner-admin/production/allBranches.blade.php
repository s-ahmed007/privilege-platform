@if(!session()->has('partner_admin'))
    <script type="text/javascript">
        window.location = "{{ url('/') }}";
    </script>
@endif
@include('partner-admin.production.header')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.css"/>

<div class="right_col" role="main">
    <div class="page-title">
        <div class="title_left">
            @if (session('status'))
                <div class="alert alert-success">
                    {{ session('status') }}
                </div>
            @elseif (session('delete branch'))
                <div class="alert alert-danger">
                    {{ session('delete branch') }}
                </div>
            @elseif (session('delete partner'))
                <div class="alert alert-danger">
                    {{ session('delete partner') }}
                </div>
            @elseif(session('try_again'))
                <div class="alert alert-warning">
                    {{ session('try_again') }}
                </div>
            @elseif(session('updated'))
                <div class="alert alert-success">
                    {{ session('updated') }}
                </div>
            @else

            @endif
            <h3>List of branches (<?php  if (isset($allBranches)) {
                    echo count($allBranches->branches);
                }?>)</h3>
        </div>
    </div>
    <div class="clearfix"></div>
    <div class="row">
        <div class="col-md-12">
            <div class="table">
                <div class="row header table_row">
                    <div class="cell">
                        S/N
                    </div>
                    <div class="cell">
                        Area
                    </div>
                    <div class="cell">
                        E-mail
                    </div>
                    <div class="cell">
                        Mobile
                    </div>
                    <div class="cell">
                        Address
                    </div>
                    <div class="cell">
                        Action
                    </div>
                </div>
                @if(isset($allBranches->branches))
                    <?php $serial = 1; ?>
                    @foreach ($allBranches->branches as $branch)
                        <div class="row table_row">
                            <div class="cell">
                                {{ $serial }}
                            </div>
                            <div class="cell">
                                {{ $branch->partner_area }}
                            </div>
                            <div class="cell">
                                {{ $branch->partner_email }}
                            </div>
                            <div class="cell">
                                {{ $branch->partner_mobile }}
                            </div>
                            <div class="cell">
                                {{ $branch->partner_address }}
                            </div>
                            <div class="cell">
                                <a href="{{ url('editBranch/'.$branch->id) }}"
                                   class="btn btn-primary">Edit</a>
                            </div>
                        </div>
                        <?php $serial++; ?>
                    @endforeach
                @endif
            </div>
        </div>
    </div>
</div>

@include('partner-admin.production.footer')
