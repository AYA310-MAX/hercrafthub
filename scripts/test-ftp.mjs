import ftp from 'basic-ftp';
import fs from 'fs';
import path from 'path';
import { fileURLToPath } from 'url';

const __dirname = path.dirname(fileURLToPath(import.meta.url));
const text = fs.readFileSync(path.join(__dirname, '../config/deploy.local.php'), 'utf8');
const get = (n) => text.match(new RegExp(`define\\('${n}',\\s*'([^']*)'\\)`))?.[1] ?? '';

const password = get('FTP_PASS');
const user = get('FTP_USER');
const hosts = ['ftpupload.net', 'ftp.infinityfree.com', 'files.000webhost.com'];

for (const host of hosts) {
  const client = new ftp.Client(30000);
  try {
    await client.access({ host, user, password, secure: false });
    console.log('OK:', host);
    client.close();
    break;
  } catch (e) {
    console.log('FAIL:', host, '-', e.message);
  }
}
