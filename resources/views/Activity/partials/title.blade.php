@if(!emptyOrHasEmptyTemplate($titles))
    <div class="activity-element-wrapper">
        <div class="activity-element-list">
            <div class="activity-element-label">@lang('activityView.activity_title')</div>
            <div class="activity-element-info">
                {{ $titles[0]['narrative']}}
                <em>(language: {{ getLanguage($titles[0]['language']) }})</em>
                @include('Activity.partials.viewInOtherLanguage', ['otherLanguages' => $titlesExceptFirstElement])
            </div>
        </div>
        <a href="{{route('activity.title.index', $id)}}" class="edit-element">edit</a>
        <a href="{{route('activity.delete-element', [$id, 'title'])}}" class="delete pull-right">remove</a>
    </div>
@endif
