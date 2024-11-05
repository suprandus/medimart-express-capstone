<div class="tab-pane fade" id="chatbot-setting" role="tabpanel" aria-labelledby="list-pusher-list">
    <div class="card border">
        <div class="card-body">
            <form action="{{route('admin.chatbot-setting-update')}}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <div class="form-group">
                    <label>Project ID</label>
                    <input type="text" class="form-control" name="project_id"
                        value="{{ $chatbotSetting?->project_id }}">
                </div>

                <div class="form-group">
                    <label>URL</label>
                    <input type="text" class="form-control" name="url" value="{{ $chatbotSetting?->url }}">
                </div>

                <div class="form-group">
                    <label>Version ID</label>
                    <input type="text" class="form-control" name="version_id"
                        value="{{ $chatbotSetting?->version_id }}">
                </div>

                <button type="submit" class="btn btn-primary">Update</button>
            </form>
        </div>
    </div>
</div>