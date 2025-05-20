@extends('layout.master')
@section('title')
    Añadir nueva categoria
@endsection
@section('main_content')
    <!-- Page content area start -->

        <div class="container">
            <!-- sign up page start-->


                <div class="card mt-4 p-4 " >
                    <div class="text-center"><img src="assets/images/endless-logo.png" alt=""></div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-vertical__item bg-style">
                                    <div class="item-top mb-30">
                                        <h2>{{ __('Añadir nueva categoria') }}</h2>
                                    </div>
                                    <form id="Form">
                                        @csrf

                                        <div class="input__group mb-25">
                                            <label for="name"> {{ __('Nombre') }} </label>
                                            <input type="text" name="name" id="name" value="{{ old('name') }}"
                                                class="form-control flat-input" placeholder="{{ __('Nombre') }}">
                                            @if ($errors->has('name'))
                                                <span class="text-danger"><i class="fas fa-exclamation-triangle"></i>
                                                    {{ $errors->first('name') }}</span>
                                            @endif
                                        </div>

                                        <div class="input__group mb-25">
                                            <label for="is_feature"> {{ __('Función') }} </label>
                                            <div>
                                                <label class="text-black"> <input type="checkbox" name="is_feature"
                                                        id="is_feature" value="yes"
                                                        {{ old('is_feature') == 'yes' ? 'checked' : '' }}>
                                                    {{ __('Yes') }} </label>
                                            </div>
                                        </div>

                                        <div class="custom-form-group mb-25">
                                            <label for="image" class="text-lg-right text-black mb-2">
                                                {{ __('Imagen') }} </label>
                                            <div class="upload-img-box mb-25">
                                                <img src="">
                                                <input type="file" name="image" id="image" accept="image/*"
                                                    onchange="previewFile(this)">
                                                <div class="upload-img-box-icon">
                                                    <i class="fa fa-camera"></i>
                                                    <p class="m-0">{{ __('Imagen') }}</p>
                                                </div>
                                            </div>
                                            @if ($errors->has('image'))
                                                <span class="text-danger"><i class="fas fa-exclamation-triangle"></i>
                                                    {{ $errors->first('image') }}</span>
                                            @endif
                                            <p>{{ __('Archivos Aceptados') }}: PNG <br> {{ __('Tamaño Recomendado') }}: 60 x
                                                60 (1MB)</p>
                                        </div>

                                        <div class="input__group mb-25">
                                            <label>{{ __('Título Meta') }}</label>
                                            <input type="text" name="meta_title" value="{{ old('meta_title') }}"
                                                placeholder="{{ __('Título Meta') }}" class="form-control">
                                            @if ($errors->has('meta_title'))
                                                <span class="text-danger"><i class="fas fa-exclamation-triangle"></i>
                                                    {{ $errors->first('meta_title') }}</span>
                                            @endif
                                        </div>

                                        <div class="input__group mb-25">
                                            <label>{{ __('Descripción Meta') }}</label>
                                            <input type="text" name="meta_description"
                                                value="{{ old('meta_description') }}"
                                                placeholder="{{ __('Descripción Meta') }}" class="form-control">
                                            @if ($errors->has('meta_description'))
                                                <span class="text-danger"><i class="fas fa-exclamation-triangle"></i>
                                                    {{ $errors->first('meta_description') }}</span>
                                            @endif
                                        </div>

                                        <div class="input__group mb-25">
                                            <label>{{ __('Palabras Clave Meta') }}</label>
                                            <input type="text" name="meta_keywords" value="{{ old('meta_keywords') }}"
                                                placeholder="{{ __('Palabras Clave Meta') }}" class="form-control">
                                            @if ($errors->has('meta_keywords'))
                                                <span class="text-danger"><i class="fas fa-exclamation-triangle"></i>
                                                    {{ $errors->first('meta_keywords') }}</span>
                                            @endif
                                        </div>

                                        <div class="input__group mb-25">
                                            <label>{{ __('Imagen OG') }}</label>
                                            <div class="upload-img-box">
                                                <img src="">
                                                <input type="file" name="og_image" id="og_image" accept="image/*"
                                                    onchange="previewFile(this)">
                                                <div class="upload-img-box-icon">
                                                    <i class="fa fa-camera"></i>
                                                    <p class="m-0">{{ __('Imagen OG') }}</p>
                                                </div>
                                            </div>
                                            @if ($errors->has('og_image'))
                                                <span class="text-danger"><i class="fas fa-exclamation-triangle"></i>
                                                    {{ $errors->first('og_image') }}</span>
                                            @endif
                                            <p><span class="text-black">{{ __('Archivos Aceptados') }}:</span> PNG, JPG <br>
                                                <span class="text-black">{{ __('Tamaño Recomendado') }}:</span> 1200 x 627
                                            </p>
                                        </div>
                                        <div class="input__group mb-25">
                                            <div class="">
                                                <button class="btn btn-primary" type="submit">Guardar</button>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

        </div>

    <!-- Page content area end -->
<!-- jQuery (required) -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
         $(document).ready(function() {
        $('#Form').submit(function(e) {
            e.preventDefault();

            let formData = new FormData(this);

            $.ajax({
                type: "POST",
                url: "{{ route('category.store') }}",
                data: formData,
                dataType: "json",
                contentType: false,
                processData: false,
                success: function(response) {
                    if (response.success) {
                        toastr.success("Categoría creada exitosamente.", "", {
                            onHidden: function() {
                                window.location.href = "{{ route('category.index') }}";
                            }
                        });
                    } else {
                        toastr.error(response.message || "No se pudo crear la categoría.");
                    }
                },
                error: function(xhr, status, error) {
                    console.error(xhr.responseText);
                    toastr.error("No se pudo crear la categoría. Por favor, inténtelo de nuevo más tarde.");
                }
            });
        });
    });
    </script>
@endsection
