<x-filament-widgets::widget>
    <x-filament::section heading="С чего начать">
        <div class="legion-admin-shortcuts">
            @foreach ($links as $link)
                <a href="{{ $link['href'] }}"><strong>{{ $link['title'] }}</strong><span>{{ $link['text'] }}</span></a>
            @endforeach
        </div>
        <p class="legion-admin-hint">Изменения появляются на сайте после сохранения. Существующие адреса страниц сохраняйте. В карточке автомобиля одна кнопка сохраняет данные всех вкладок.</p>
    </x-filament::section>
</x-filament-widgets::widget>
