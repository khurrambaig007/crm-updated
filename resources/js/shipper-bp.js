document.addEventListener('DOMContentLoaded', function () {
    var portSelect = document.getElementById('port_id');
    var portCodeInput = document.getElementById('port_code');

    if (!portSelect || !portCodeInput) return;

    var portsMap = JSON.parse(portSelect.dataset.ports || '{}');

    function syncPortCode() {
        portCodeInput.value = portsMap[portSelect.value] || '';
    }

    portSelect.addEventListener('change', syncPortCode);
    syncPortCode();
});
