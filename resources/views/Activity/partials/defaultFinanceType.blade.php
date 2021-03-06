@if(!empty($defaultFinanceType))
    <div class="activity-element-wrapper">
        <div class="activity-element-list">
            <div class="activity-element-label">@lang('activityView.default_finance_type')</div>
            <div class="activity-element-info">
                {{ substr($getCode->getActivityCodeName('FinanceType', $defaultFinanceType) , 0 , -5)}}
            </div>
        </div>
        <a href="{{route('activity.default-finance-type.index', $id)}}" class="edit-element">edit</a>
        <a href="{{route('activity.delete-element', [$id, 'default_finance_type'])}}" class="delete pull-right">remove</a>
    </div>
@endif
