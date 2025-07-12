{{-- SB Admin 2 Logout Modal --}}
<!-- Logout Modal-->
<div class="modal fade" id="logoutModal" tabindex="-1" role="dialog" aria-labelledby="logoutModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="logoutModalLabel">
                    <i class="fas fa-sign-out-alt"></i>
                    Confirmer la Déconnexion
                </h4>
                <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
            </div>
            <div class="modal-body">
                <p class="mb-0">
                    <i class="fas fa-question-circle text-warning"></i>
                    Êtes-vous sûr de vouloir vous déconnecter de votre session ?
                </p>
            </div>
            <div class="modal-footer">
                <button class="btn btn-secondary" type="button" data-dismiss="modal">
                    <i class="fas fa-times"></i>
                    Annuler
                </button>
                <form method="POST" action="{{ route('logout') }}" style="display: inline;">
                    @csrf
                    <button type="submit" class="btn btn-danger">
                        <i class="fas fa-sign-out-alt"></i>
                        Se Déconnecter
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
