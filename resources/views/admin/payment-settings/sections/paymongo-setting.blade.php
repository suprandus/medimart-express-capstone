<div class="tab-pane fade show active" id="list-paymongo" role="tabpanel" aria-labelledby="list-paymongo-list">
  <div class="card border">
    <div class="card-body">
      <form action="{{route('admin.paymongo-setting.update', 1)}}" method="POST">
        @csrf
        @method('PUT')

        <div class="form-group">
          <label>PayMongo Status</label>
          <select name="status" id="" class="form-control">
            <option {{$paymongoSetting->paymongo_status === 1 ? 'selected' : ''}} value="1">Enable</option>
            <option {{$paymongoSetting->paymongo_status === 0 ? 'selected' : ''}} value="0">Disable</option>
          </select>
        </div>

        <div class="form-group">
          <label>Live Mode</label>
          <select name="mode" id="" class="form-control">
            <option {{$paymongoSetting->live_mode === 1 ? 'selected' : ''}} value="1">True</option>
            <option {{$paymongoSetting->live_mode === 0 ? 'selected' : ''}} value="0">False</option>
          </select>
        </div>

        <div class="form-group">
          <label>PayMongo Public Key</label>
          <input type="text" class="form-control" name="public_key" value="{{$paymongoSetting->public_key}}">
        </div>

        <div class="form-group">
          <label>PayMongo Secret Key</label>
          <input type="text" class="form-control" name="secret_key" value="{{$paymongoSetting->secret_key}}">
        </div>

        <button type="submit" class="btn btn-primary">Update</button>
      </form>
    </div>
  </div>
</div>