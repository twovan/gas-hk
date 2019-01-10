
{{ csrf_field() }}
<input type="hidden" id="form-id" name="id">
<div class="form-group form-group-sm">
    <label class="col-sm-3 control-label">油枪号</label>
    <div class="col-sm-8">
        <input type="text" class="form-control" name="number" id="form-number" required>
    </div>

</div>



<div class="form-group form-group-sm">
    <label class="col-sm-3 control-label">油品型号</label>
    <div class="col-sm-8">
        @if(!empty($gas_no_list))
            <select class="form-control" name="gas_no" id="form-gas_no" required>
                @foreach($gas_no_list as $gas_no)
                    <option value="{{$gas_no}}">{{$gas_no}}#</option>
                @endforeach
            </select>
            @else
            <span>暂无油品型号，请先在油品管理中添加</span>
        @endif
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