<article class="uk-comment uk-comment-primary uk-margin-small" role="comment">
    <header class="uk-comment-header">
        <div class="uk-grid-medium uk-flex-middle" uk-grid>

            <div class="uk-width-expand">
                <h4 class="uk-comment-title uk-margin-remove"><span class="uk-link-reset">{{ $Comment->author }}, {{$Comment->subject}}</span></h4>
                @if (!empty($Comment->created_at))
                    <ul class="uk-comment-meta uk-subnav uk-subnav-divider uk-margin-remove-top">
                        <li><span>{{ date("d.m.Y H:i", strtotime($Comment->created_at)) }}</span></li>
                    </ul>
                @endif
            </div>
        </div>
    </header>

    @if ($Comment->grade > 0)
        <div class="uk-margin">

                @for ($i = 1; $i <= $Comment->grade; $i++)
                    <span @class([
                        "green" => $Comment->grade == 5 ? true : false,
                        "yellow" => $Comment->grade == 4 ? true : false,
                        "red" => $Comment->grade == 3 ? true : false,
                        "maroon" => $Comment->grade < 3 ? true : false,
                        "uk-icon-link",
                        "uk-margin-small-right",
                    ]) uk-icon="star"></span>
                @endfor

        </div> 
    @endif
    
    <div class="uk-comment-body">
        {!! $Comment->text !!}
    </div>

    @if (!isset($shopItem))
        @php

        $CommentShopItem = $Comment->CommentShopItem;
        @endphp

        @if (!is_null($CommentShopItem))
            <header class="uk-comment-header uk-margin">
                <div class="uk-grid-medium uk-flex-middle" uk-grid>
                    <div class="uk-width-auto">
                        @foreach ($CommentShopItem->ShopItem->getImages(false) as $image)
                            <img class="uk-comment-avatar" src="{{ $image['image_small'] }}" width="80" height="80" alt="">
                        @endforeach  
                    </div>
                    <div class="uk-width-expand">
                        <div class="uk-h4 uk-comment-title uk-margin-remove"><a class="uk-link-reset" href="{{ $CommentShopItem->ShopItem->url }}">{{ $CommentShopItem->ShopItem->name }}</a></div> 
                    </div>
                </div>
            </header>
        @endif
    @endif

</article>

@if ($SubComments = \App\Models\Comment::where("parent_id", $Comment->id)->where("active", 1)->get())
    <div class="uk-margin-left">
        @foreach ($SubComments as $Comment)            
            @include('comment.comment', [
                'Comment' => $Comment,
            ])
        @endforeach  
    </div>
@endif