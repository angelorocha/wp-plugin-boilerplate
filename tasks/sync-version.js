const fs = require('fs');
const path = require('path');

module.exports = function (gulp, plugins, config) {
	gulp.task('sync-version', function (done) {
		const rootDir = './';
		const pkgFilePath = './package.json';
		const phpFiles = fs.readdirSync(rootDir).filter(file => file.endsWith('.php'));
		let mainPhpFile = null;
		let phpContent = '';
		for (const file of phpFiles) {
			const content = fs.readFileSync(path.join(rootDir, file), 'utf8');
			if (/Plugin Name\s*:/i.test(content)) {
				mainPhpFile = file;
				phpContent = content;
				break;
			}
		}
		if (mainPhpFile && phpContent) {
			const match = phpContent.match(/(?:Version:|\* @version)\s*([\d\.]+)/i);

			if (match && match[1]) {
				const phpVersion = match[1];
				const pkg = JSON.parse(fs.readFileSync(pkgFilePath, 'utf8'));

				if (pkg.version !== phpVersion) {
					pkg.version = phpVersion;
					fs.writeFileSync(pkgFilePath, JSON.stringify(pkg, null, 2) + '\n');
					console.log(`\x1b[32m[Version Sync]\x1b[0m package.json atualizado para v${phpVersion} (${mainPhpFile})`);
				}
			}
		}
		done();
	});
};
