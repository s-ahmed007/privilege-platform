@include('admin.production.header')

<div class="right_col" role="main">
    <div class="page-title">
        <div class="title_left">
            @if (session('added'))
                <h3>All Hotspots</h3>
                <div class="alert alert-success">
                    {{ session('status') }}
                </div>
            @elseif (session('deletd'))
                <h3>All Hotspots</h3>
                <div class="alert alert-danger">
                    {{ session('delete partner') }}
                </div>
            @elseif(session('try_again'))
                <h3>All Hotspots</h3>
                <div class="alert alert-warning">
                    {{ session('try_again') }}
                </div>
            @else
                <h3>All Hotspots</h3>
            @endif
        </div>
    </div>
    <div class="clearfix"></div>
    <div class="row">
        <div class="col-md-12">
            <div class="x_panel">
                <div class="x_content">
                    @if($allHotspots)
                        <table class="table table-striped projects">
                            <thead>
                            <tr>
                                <th>ID</th>
                                <th>Image</th>
                                <th>Name</th>
                                <th>Edit</th>
                                <th>Delete</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach ($allHotspots as $allHotspot)
                                <tr>
                                    <td>{{ $allHotspot->id }}</td>
                                    <td><img src="{{ $allHotspot->image_link }}" alt="hotspot profile image" width="360"
                                             height="250"></td>
                                    <td>{{ $allHotspot->name }}</td>
                                    <td><a href="{{ url('edit_hotspot/'.$allHotspot->id) }}">
                                            <button class="btn btn-primary" disabled>Edit</button>
                                        </a></td>
                                    <td><a href="{{ url('delete_hotspot/'.$allHotspot->id) }}">
                                            <button class="btn btn-danger"
                                                    onclick="return confirm('Are you sure you want to delete this hotspot')">
                                                Delete
                                            </button>
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    @else
                        <div style="font-size: 1.4em; color: red;">
                            {{ 'Id not found.' }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

@include('admin.production.footer')