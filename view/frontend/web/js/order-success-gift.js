define(['jquery', 'Swiftgift_Gift/js/lib/clipboard.min'], function(
    $,
    ClipboardJS
) {
    return function(config, elem) {
        var cb = new ClipboardJS('.clipboard-btn');
        cb.on('success', function() {
            alert('Magic link copied to clipboard!');
        });
        cb.on('error', function(e) {
            console.log('error', e);
        });
    };
});
