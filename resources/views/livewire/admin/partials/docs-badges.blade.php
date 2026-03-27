<div class="docs-section-header">
    <h2 class="docs-section-title">
        <i class="fas fa-tag"></i>
        Badges
    </h2>
    <p class="docs-section-desc">Small count and labeling component for status indicators.</p>
</div>

<div class="docs-card">
    <div class="docs-card-header">
        <h3 class="docs-card-title">
            <i class="fas fa-palette"></i>
            Badge Variants
        </h3>
        <div class="docs-card-actions">
            <button class="docs-copy-btn" onclick="copyCode(this)">
                <i class="fas fa-copy"></i> Copy
            </button>
        </div>
    </div>
    <div class="docs-code">
        <pre><code>&lt;x-badge variant="primary"&gt;Primary&lt;/x-badge&gt;
&lt;x-badge variant="success" icon="fas fa-check"&gt;Success&lt;/x-badge&gt;
&lt;x-badge variant="warning" icon="fas fa-clock"&gt;Pending&lt;/x-badge&gt;
&lt;x-badge variant="danger" icon="fas fa-times"&gt;Error&lt;/x-badge&gt;
&lt;x-badge variant="info" icon="fas fa-info"&gt;Info&lt;/x-badge&gt;</code></pre>
    </div>
    <div class="docs-preview">
        <div class="docs-preview-label">Live Preview</div>
        <div class="d-flex flex-wrap gap-2">
            <x-badge variant="primary">Primary</x-badge>
            <x-badge variant="success" icon="fas fa-check">Success</x-badge>
            <x-badge variant="warning" icon="fas fa-clock">Pending</x-badge>
            <x-badge variant="danger" icon="fas fa-times">Error</x-badge>
            <x-badge variant="info" icon="fas fa-info">Info</x-badge>
        </div>
    </div>
</div>

<div class="docs-card">
    <div class="docs-card-header">
        <h3 class="docs-card-title">
            <i class="fas fa-cog"></i>
            Props
        </h3>
    </div>
    <div class="docs-preview p-0">
        <table class="docs-props-table">
            <thead>
                <tr>
                    <th>Prop</th>
                    <th>Type</th>
                    <th>Default</th>
                    <th>Description</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td><code>variant</code></td>
                    <td>string</td>
                    <td><code>primary</code></td>
                    <td>primary, success, warning, danger, info, secondary</td>
                </tr>
                <tr>
                    <td><code>icon</code></td>
                    <td>string</td>
                    <td><code>null</code></td>
                    <td>FontAwesome icon class</td>
                </tr>
            </tbody>
        </table>
    </div>
</div>