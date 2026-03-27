<div class="docs-section-header">
    <h2 class="docs-section-title">
        <i class="fas fa-exclamation-circle"></i>
        Alerts
    </h2>
    <p class="docs-section-desc">Contextual feedback messages for user actions.</p>
</div>

<div class="docs-card">
    <div class="docs-card-header">
        <h3 class="docs-card-title">
            <i class="fas fa-palette"></i>
            Alert Variants
        </h3>
        <div class="docs-card-actions">
            <button class="docs-copy-btn" onclick="copyCode(this)">
                <i class="fas fa-copy"></i> Copy
            </button>
        </div>
    </div>
    <div class="docs-code">
        <pre><code>&lt;x-alert variant="success" title="Success!"&gt;
    Operation completed successfully.
&lt;/x-alert&gt;

&lt;x-alert variant="danger" title="Error!"&gt;
    Something went wrong.
&lt;/x-alert&gt;

&lt;x-alert variant="warning" title="Warning!"&gt;
    Please check your input.
&lt;/x-alert&gt;

&lt;x-alert variant="info" title="Info"&gt;
    You have new notifications.
&lt;/x-alert&gt;</code></pre>
    </div>
    <div class="docs-preview">
        <div class="docs-preview-label">Live Preview</div>
        <x-alert variant="success" title="Success!" class="mb-3">
            Operation completed successfully.
        </x-alert>
        <x-alert variant="danger" title="Error!" class="mb-3">
            Something went wrong.
        </x-alert>
        <x-alert variant="warning" title="Warning!" class="mb-3">
            Please check your input.
        </x-alert>
        <x-alert variant="info" title="Info">
            You have new notifications.
        </x-alert>
    </div>
</div>