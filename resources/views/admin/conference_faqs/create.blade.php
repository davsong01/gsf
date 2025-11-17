@extends('layouts.dashboard')
@section('title', isset($faq) ? 'Edit Conference FAQ' : 'Create Conference FAQ')

@section('content')
<div class="content-body">
    <div class="card">
        <div class="card-header">
            <h4 class="card-title">{{ isset($faq) ? 'Edit Conference FAQ' : 'Add Conference FAQ' }}</h4>
            @include('includes.alerts')
        </div>

        <div class="card-body">
            <form action="{{ isset($faq) ? route('conference_faqs.update', $faq->id) : route('conference_faqs.store') }}" method="POST">
                @csrf
                @if(isset($faq))
                    @method('PUT')
                @endif

                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group mb-3">
                            <label for="question">Question</label>
                            <input 
                                type="text" 
                                name="question" 
                                id="question" 
                                value="{{ old('question', $faq->question ?? '') }}" 
                                class="form-control" 
                                placeholder="Enter the FAQ question" 
                                required
                            >
                            @error('question') 
                                <small class="text-danger">{{ $message }}</small> 
                            @enderror
                        </div>
                    </div>

                    <div class="col-md-12">
                        <div class="form-group mb-3">
                            <label for="answer">Answer</label>
                            <textarea 
                                name="answer" 
                                id="answer" 
                                class="form-control" 
                                rows="4" 
                                placeholder="Provide the answer here" 
                                required>{{ old('answer', $faq->answer ?? '') }}</textarea>
                            @error('answer') 
                                <small class="text-danger">{{ $message }}</small> 
                            @enderror
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group mb-3">
                            <label for="status">Status</label>
                            <select name="status" id="status" class="form-control" required>
                                <option value="1" {{ old('status', $faq->status ?? '') == 1 ? 'selected' : '' }}>Active</option>
                                <option value="0" {{ old('status', $faq->status ?? '') == 0 ? 'selected' : '' }}>Inactive</option>
                            </select>
                            @error('status') 
                                <small class="text-danger">{{ $message }}</small> 
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group mb-3">
                            <label for="question">Display Order</label>
                            <input 
                                type="text" 
                                name="display_order" 
                                id="display_order" 
                                value="{{ old('display_order', $faq->display_order ?? '') }}" 
                                class="form-control" 
                                placeholder="Enter the FAQ display order" 
                                required
                            >
                            @error('display_order') 
                                <small class="text-danger">{{ $message }}</small> 
                            @enderror
                        </div>
                    </div>
                </div>

                <button type="submit" class="btn btn-primary mt-2">
                    {{ isset($faq) ? 'Update FAQ' : 'Create FAQ' }}
                </button>
            </form>
        </div>
    </div>
</div>
@endsection
