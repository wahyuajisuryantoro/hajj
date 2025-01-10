@extends('pages.account.index')
@section('account-content')
    <div class="card mb-6">
        <h5 class="card-header">Change Password</h5>
        <div class="card-body pt-1">
            <form id="formUpdatePassword" method="POST" action="{{ route('account.update-password') }}">
                @csrf
                <div class="row">
                    <div class="mb-5 col-md-6 form-password-toggle">
                        <div class="input-group input-group-merge">
                            <div class="form-floating form-floating-outline">
                                <input class="form-control @error('current_password') is-invalid @enderror" type="password"
                                    name="current_password" id="current_password"
                                    placeholder="&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;"
                                    required />
                                <label for="current_password">Current Password</label>
                                @error('current_password')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <span class="input-group-text cursor-pointer toggle-password"><i
                                    class="ri-eye-off-line"></i></span>
                        </div>
                    </div>
                </div>
                <div class="row g-5 mb-6">
                    <div class="col-md-6 form-password-toggle">
                        <div class="input-group input-group-merge">
                            <div class="form-floating form-floating-outline">
                                <input class="form-control @error('new_password') is-invalid @enderror" type="password"
                                    name="new_password" id="new_password"
                                    placeholder="&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;"
                                    required />
                                <label for="new_password">New Password</label>
                                @error('new_password')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <span class="input-group-text cursor-pointer toggle-password"><i
                                    class="ri-eye-off-line"></i></span>
                        </div>
                    </div>
                    <div class="col-md-6 form-password-toggle">
                        <div class="input-group input-group-merge">
                            <div class="form-floating form-floating-outline">
                                <input class="form-control @error('confirm_password') is-invalid @enderror" type="password"
                                    name="confirm_password" id="confirm_password"
                                    placeholder="&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;"
                                    required />
                                <label for="confirm_password">Confirm New Password</label>
                                @error('confirm_password')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <span class="input-group-text cursor-pointer toggle-password"><i
                                    class="ri-eye-off-line"></i></span>
                        </div>
                    </div>
                </div>
                <h6 class="text-body">Password Requirements:</h6>
                <ul class="ps-4 mb-0">
                    <li class="mb-4">Minimum 8 characters long - the more, the better</li>
                    <li class="mb-4">At least one lowercase character</li>
                    <li>At least one number, symbol, or whitespace character</li>
                </ul>
                <div class="mt-6">
                    <button type="submit" class="btn btn-primary me-3">Save changes</button>
                    <button type="reset" class="btn btn-outline-secondary">Reset</button>
                </div>
            </form>
        </div>
    </div>
@endsection
