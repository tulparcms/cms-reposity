<form method="POST" action="{{ route('admin.reposity.download') }}" class="ajax-form">
    <div class="row mb-3">
        <label class="col-sm-3 col-form-label" for="basic-icon-default-fullname">{{ __(REPOSITY_LANG.'Input A Github Url') }}</label>
        <div class="col-sm-9">
            <div class="input-group input-group-merge">
                <span id="basic-icon-default-fullname2" class="input-group-text"><i class="ti ti-brand-github"></i></span>
                <input name="reposity" type="text" class="form-control" placeholder="https://github.com/tulparcms/cms" >
            </div>
        </div>
    </div>
    <div class="pt-4">


        <div class="row justify-content-end">
            <div class="col-sm-9 text-end">
                @csrf
                <button data-name="process" type="submit" class="btn btn-label-success" value="download">
                    <i class="tf-icons ti ti-download"></i> {{ __(REPOSITY_LANG.'Download') }}
                </button>
                <button data-name="process" type="submit" class="btn btn-label-dark" value="check">
                    <i class="tf-icons ti ti-settings-cog"></i> {{ __(REPOSITY_LANG.'Check') }}
                </button>
                <input type="hidden" name="process" class="from-button">
            </div>
        </div>
    </div>
</form>
