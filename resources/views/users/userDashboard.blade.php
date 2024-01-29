@extends('layouts.navbar')
@section('content')
<style>
.card-header{
    background-color:#29ADB2;
}
.card-body{
    background-color: #C5E898;
    height:100%;
}
.card{
    height:100%;
}

.row{
    padding:2%;
    height:100%;
}
</style>
<div class="container">
    <div class="content">
        <div class="dashboard-content px-3 pt-4">
                <h2 class="fs-5"> User Dashboard</h2>
                <hr style="border-top: 2px solid #000;">
            <div class="row">
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header">Total Users</div>
                        <div class="card-body">
                            <h1>{{Auth::user()->count() }}</h1>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header">Available Lockers</div>
                        <div class="card-body">
                            <h1>{{ DB::table('locker')->count() }}</h1>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

