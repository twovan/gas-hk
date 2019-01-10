
{{ csrf_field() }}
<input type="hidden" id="form-id" name="id">
<div class="form-group form-group-sm">
    <label class="col-sm-3 control-label">姓名</label>
    <div class="col-sm-8">
        <input type="text" class="form-control" name="wx_name" id="form-name">
    </div>
</div>
<div class="form-group form-group-sm">
    <label class="col-sm-3 control-label">手机号</label>
    <div class="col-sm-8">
        <input type="text" class="form-control" name="phone" id="form-phone" readonly="readonly">
    </div>
</div>

<div class="form-group form-group-sm">
    <label class="col-sm-3 control-label">身份</label>
    <div class="col-sm-8">
        <select class="form-control" name="vip" id="form-vip">
            @foreach(config('params')['vip_type'] as $key => $value)
                <option value="{{$key}}">{{$value}}</option>
            @endforeach
        </select>
    </div>
</div>

<div class="form-group form-group-sm">
    <label class="col-sm-3 control-label">状态</label>
    <div class="col-sm-8">
        <select class="form-control" name="status" id="form-status">
            @foreach(config('params')['status'] as $key => $value)
                <option value="{{$key}}">{{$value}}</option>
            @endforeach
        </select>
    </div>
</div>

