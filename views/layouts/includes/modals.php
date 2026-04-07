<!-- Upload files -->
<div class="modal fade" id="upload-files" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Upload file</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form action="/portal/application/upload-files" id="upload-form" enctype="multipart/form-data"
                    method="POST">
                    <div class="custom-file">
                        <input type="file" class="custom-file-input" id="files" name="files[]" accept=".pdf,.doc,.docx">
                        <label class="custom-file-label" for="files">Choose file</label>
                    </div>

                    <div id="file-list" class="mt-2"></div>

                    <input type="hidden" id="application-id" name="application-id" value="">
                    <input type="hidden" name="_csrf" class="csrf" value="<?= Yii::$app->request->getCsrfToken() ?>" />
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary d-flex align-items-center" id="upload-files-btn">Upload
                    files</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="add-note-modal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add a note</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form>
                    <div class="form-group">
                        <textarea class="form-control" name="note" id="note" placeholder="Add a note"></textarea>
                    </div>
                    <input type="hidden" id="application-id" name="application-id" value="">
                    <input type="hidden" name="_csrf" class="csrf" value="<?= Yii::$app->request->getCsrfToken() ?>" />
                </form>
            </div>
            <div class="modal-footer">
                <button type="button"
                    class="btn btn-primary d-flex align-items-center justify-content-center save-note-btn">Save
                    note</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="add-ein-modal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add EIN</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <input type="text" class="form-control ein-number" placeholder="Enter EIN" />
                </div>
            </div>
            <div class="modal-footer">
                <button type="button"
                    class="btn btn-primary d-flex align-items-center justify-content-center save-ein">Save EIN</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="delete-confirmation" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Delete Record</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                Are you sure you want to delete?
            </div>
            <div class="modal-footer">
                <a href="#" type="button"
                    class="btn btn-danger d-flex align-items-center justify-content-center confirm-delete-btn">Delete</a>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="app-delete-confirmation" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Delete Record</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to delete?</p>
                <div class="form-group">
                    <label for="delete-password">Enter password:</label>
                    <input type="password" id="delete-password" class="form-control" placeholder="Password" required>
                    <small class="text-danger d-none" id="delete-error">Password is required.</small>
                </div>
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" value="1" name="confirmation" id="send_confirmation">
                    <label class="form-check-label" for="send_confirmation">
                       Send Confirmation Email
                    </label>
                </div>
            </div>
            <div class="modal-footer">
                <a href="#" type="button"
                    class="btn btn-danger d-flex align-items-center justify-content-center confirm-delete-btn">Delete</a>
            </div>
        </div>
    </div>
</div>


<div class="modal fade" id="refund-confirmation" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Refund?</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to refund?</p>
                <div class="form-group">
                    <label for="delete-password">Enter password:</label>
                    <input type="text" id="refund-password" class="form-control" placeholder="Password" required
                        value="">
                    <small class="text-danger d-none" id="delete-error">Password is required.</small>
                </div>
            </div>
            <div class="modal-footer">
                <a href="#"
                    class="btn btn-primary d-flex align-items-center justify-content-center confirm-refund-btn">Refund</a>
            </div>
        </div>
    </div>
</div>


<div class="modal fade" id="resend-files" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Confirm Resending Email</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                Are you sure you want to resend the EIN confirmation with the document?
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary d-flex align-items-center resend-confirmation">Resend
                    Confirmation</button>
            </div>
        </div>
    </div>
</div>