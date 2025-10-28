@if (gs('multi_language'))
    <div class="language_switcher ms-2 ms-sm-3">
        @if (gs('multi_language'))
            @php
                $language = App\Models\Language::all();
                $selectLang = $language->where('code', config('app.locale'))->first();
                $currentLang = session('lang') ? $language->where('code', session('lang'))->first() : $language->where('is_default', Status::YES)->first();
            @endphp
            <div class="language_switcher__caption">
                <span class="icon">
                    <img src="{{ getImage(getFilePath('language') . '/' . $currentLang->image, getFileSize('language')) }}" alt="@lang('image')">
                </span>
                <span class="text"> {{ __(@$selectLang->name) }} </span>
            </div>
            <div class="language_switcher__list">
                @foreach ($language as $item)
                    <div class="language_switcher__item    @if (session('lang') == $item->code) selected @endif" data-value="{{ $item->code }}">
                        <a href="{{ route('lang', $item->code) }}" class="thumb">
                            <span class="icon">
                                <img src="{{ getImage(getFilePath('language') . '/' . $item->image, getFileSize('language')) }}" alt="@lang('image')">
                            </span>
                            <span class="text"> {{ __($item->name) }}</span>
                        </a>
                    </div>
                @endforeach
            </div>
        @endif
    </div>

    @push('script')
        <script>
            (function($) {
                "use strict";
               
                $('.language_switcher > .language_switcher__caption').on('click', function() {
                    $(this).parent().toggleClass('open');
                });
                $(document).on('keyup', function(evt) {
                    if ((evt.keyCode || evt.which) === 27) {
                        $('.language_switcher').removeClass('open');
                    }
                });
                $(document).on('click', function(evt) {
                    if ($(evt.target).closest(".language_switcher > .language_switcher__caption").length === 0) {
                        $('.language_switcher').removeClass('open');
                    }
                });
            })(jQuery);
        </script>
    @endpush
    @push('style')
        <style>
            /* language */
            .language_switcher {
                position: relative;
                padding-right: 20px;
                min-width: max-content;
            }

            @media(max-width: 991px) {
                .language_switcher {
                    padding-block: 6px;
                    display: inline-flex;
                }

                .language_switcher_wrapper {
                    flex: 1;
                    text-align: right;
                }
            }

            .language_switcher::after {
                font-family: 'Line Awesome Free';
                content: "\f107";
                font-weight: 900;
                font-size: 14px;
                position: absolute;
                margin: 0;
                color: #fff;
                top: 50%;
                right: 0;
                -webkit-transform: translateY(-50%);
                transform: translateY(-50%);
                transition: all ease 350ms;
                -webkit-transition: all ease 350ms;
                -moz-transition: all ease 350ms;
            }

            .language_switcher.open:after {
                -webkit-transform: translateY(-50%) rotate(180deg);
                transform: translateY(-50%) rotate(180deg);
            }

            .language_switcher__caption {
                cursor: pointer;
                padding: 0;
                display: flex;
                align-items: center;
                gap: 4px;
                flex-wrap: nowrap;
            }

            .language_switcher__caption .icon {
                position: relative;
                height: 20px;
                width: 20px;
                display: flex;
            }

            .language_switcher__caption .icon img {
                height: 100%;
                width: 100%;
                border-radius: 50%;
                object-fit: cover;
            }

            .language_switcher__caption .text {
                font-size: 0.875rem;
                font-weight: 500;
                flex: 1;
                color: #fff;
                line-height: 1;
            }

            .language_switcher__list {
                width: 100px;
                border-radius: 4px;
                padding: 0;
                max-height: 105px;
                overflow-y: auto !important;
                background: #fff;
                -webkit-box-shadow: 0px 12px 24px rgba(21, 18, 51, 0.13);
                opacity: 0;
                overflow: hidden;
                -webkit-transition: all 0.15s cubic-bezier(0.25, 0, 0.25, 1.75),
                    opacity 0.1s linear;
                transition: all 0.15s cubic-bezier(0.25, 0, 0.25, 1.75), opacity 0.1s linear;
                -webkit-transform: scale(0.85);
                transform: scale(0.85);
                -webkit-transform-origin: 50% 0;
                transform-origin: 50% 0;
                position: absolute;
                top: calc(100% + 18px);
                z-index: -1;
                visibility: hidden;
                border: 1px solid rgb(0 0 0 / 10%);
            }

            .language_switcher__list::-webkit-scrollbar-track {
                border-radius: 3px;
                background-color: hsl(var(--base) / 0.3);
            }

            .language_switcher__list::-webkit-scrollbar {
                width: 3px;
            }

            .language_switcher__list::-webkit-scrollbar-thumb {
                border-radius: 3px;
                background-color: hsl(var(--base) / 0.8);
            }

            .language_switcher__list .text {
                font-size: 0.875rem;
                font-weight: 500;
                color: black;
            }

            .language_switcher.open .language_switcher__list {
                -webkit-transform: scale(1);
                transform: scale(1);
                opacity: 1;
                z-index: 1;
                visibility: visible;
            }

            .language_switcher__item a {
                cursor: pointer;
                padding: 5px;
                border-bottom: 1px solid hsl(var(--heading-color) / 0.2);
                display: flex;
                align-items: center;
                gap: 4px;
            }

            .language_switcher__item img {
                height: 20px;
                width: 20px;
                display: block;
                border-radius: 50%;
            }

            .language_switcher__item:last-of-type {
                border-bottom: 0;
            }

            .language_switcher__item.selected {
                background: rgba(36, 60, 187, 0.02);
                pointer-events: none;
            }
        </style>
    @endpush
@endif
