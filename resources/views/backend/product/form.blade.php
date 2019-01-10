
{{ csrf_field() }}
<input type="hidden" id="form-id" name="id">
<div class="form-group form-group-sm">
    <label class="col-sm-3 control-label">油品类型(#)</label>
    <div class="col-sm-8">
        <input type="text" class="form-control" name="gas_no" id="form-gas_no" required>
    </div>

</div>

<div class="form-group form-group-sm">
    <label class="col-sm-3 control-label">价格(元)</label>
    <div class="col-sm-8">
        <input type="text" class="form-control" name="price" id="form-price" required>
    </div>
</div>

<div class="form-group form-group-sm">
    <label class="col-sm-3 control-label">类型</label>
    <div class="col-sm-8">
        <select class="form-control" name="type" id="form-type">
            <option value="1">汽油</option>
            <option value="2">柴油</option>
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

<div class="form-group form-group-sm">
    <label class="col-sm-3 control-label">备注</label>
    <div class="col-sm-8">
        <input type="text" class="form-control" name="remark" id="form-remark" >
    </div>
</div>

<input type="hidden" id="form-option" name="option">