/*
 * This file is part of the CssModuleBundle package.
 * Copyright (c) Matthias Krauser <matthias@krauser.eu>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
module.exports = function(content, map, meta) {
    const callback = this.async();

    const regex = /\{\%\s*importModule\s+['"](.+)["'];?\s*\%\}/g;
    let match;

    let imports = [];
    // Process all require statements
    while ((match = regex.exec(content)) !== null) {
        const requiredFile = match[1];
        imports.push(`import '${requiredFile}';`);
    }

    // You might want to parse and modify the source further
    // Now call the callback with the modified source
    callback(null, imports.join("\n"), null, meta);
};