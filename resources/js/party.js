document.addEventListener('DOMContentLoaded', function () {
    var agentSelect = document.getElementById('agent_id');
    var agentNameInput = document.getElementById('agent_name');

    if (!agentSelect || !agentNameInput) return;

    var agentsMap = JSON.parse(agentSelect.dataset.agents || '{}');

    function syncAgentName() {
        agentNameInput.value = agentsMap[agentSelect.value] || '';
    }

    agentSelect.addEventListener('change', syncAgentName);
    syncAgentName();
});
