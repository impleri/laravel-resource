/**
 * API Routing for {{ $element }}
 */
Route::model('{{ $element }}', '{{ $model }}');
Route::post('{{ $basePath }}/{{ $collection }}', '{{ $controller }}@postCollection';
@if ($isCollection) {
    Route::get('{{ $basePath }}/{{ $collection }}', '{{ $controller }}@getCollection';
    @if ($allowPutAll) {
        Route::put('{{ $basePath }}/{{ $collection }}', '{{ $controller }}@putCollection';
    }
    @if ($allowDeleteAll) {
        Route::delete('{{ $basePath }}/{{ $collection }}', '{{ $controller }}@deleteCollection';
    }
}
@if ($isElement) {
    Route::get('{{ $basePath }}/{{ $collection }}/\{{{ $element }}\}', '{{ $controller }}@getElement';
    Route::put('{{ $basePath }}/{{ $collection }}/\{{{ $element }}\}', '{{ $controller }}@putElement';
    Route::patch('{{ $basePath }}/{{ $collection }}/\{{{ $element }}\}', '{{ $controller }}@patchElement';
    Route::delete('{{ $basePath }}/{{ $collection }}/\{{{ $element }}\}', '{{ $controller }}@deleteElement';
}
