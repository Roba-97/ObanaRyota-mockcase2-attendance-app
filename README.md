# CoachTech 勤怠管理アプリ

## 環境構築
1. `git clone https://github.com/Roba-97/ObanaRyota-mockcase2-attendance-app.git`
2. Docker Desctop アプリを起動
3. プロジェクト直下で `make init`

ダミーデータの作成まで完了し、勤怠管理アプリの動作確認が可能です

## 動作確認時における注意事項
- 勤怠登録画面において **「出勤」「退勤」「休憩」ボタンを短時間に何度も続けてクリックしないでください。**
- 勤怠詳細画面において **「修正」ボタンを短時間に何度も続けてクリックしないでください。**

いずれもシステムの誤動作を引き起こす可能性があります

## 使用技術
- PHP 7.4.9
- Laravel Framework 8.83.8
- mysql:8.0.26
- nginx:1.21.1
- mailhog:latest
- selenium standalone-chrome-debug:latest

## ER図
![ER図](/src/ER-diagram.drawio.png)

## テストアカウントについて
### 一般ユーザー
| メールアドレス | パスワード | 
| --- | --- |
| `test@example.com` | `password` |

### 管理者ユーザー
| メールアドレス | パスワード | 
| --- | --- |
| `admin@example.com` | `password` |

## phpunitを利用したテストについて
### テストの実行準備
テスト用のデータベースを以下の手順で作成してください
1. `docker-compose exec mysql bash`
2. `mysql -u root -p`
3. パスワード `root` を入力
4. `CREATE DATABASE test_db;`
5. `SHOW DATABASES;` で「test_db」データベースが作成されていることを確認

### テストの実行
以下の手順でテストを実行できます
1. `docker-compose exec php bash`
2. `php artisan test test/Feature/テストファイル名` または `php artisan dusk test/Browser/テストファイル名`

※ 作成したテストファイルには、対象のテストケースID及びテスト内容番号（上から1,2, ... ）を記しています<br>
※ 下のトグルを展開することで各テストケースと内容がどのファイルで実行されるかの対応表を確認いただけます

<details>

<summary>テストケース対応表</summary>

  | ID | 内容番号 | ディレクトリ | ファイル名 |
  | --- | --- | --- | --- |
  | 1 | all | Feature | UserRegistrationTest.php |
  | 2,3 | all | Feature | AuthenticationTest.php |
  | 4,5 | all | Feature | AttendanceViewTest.php |
  | 6,7,8 | 最後以外<br>最後 | Browser<br>Feature | StampTest.php<br>StampConfirmTest.php |
  | 9 | all | Browser | UserAttendanceListTest.php |
  | 10 | all | Feature | AttendanceDetailTest.php |
  | 11 | 1～4<br>5～ | Feature<br>Browser | ModificationRequestTest.php<br>ModificationRequestTest.php |
  | 12 | all | Browser | UserAttendanceListTest.php |
  | 13 | all | Feature | AdminAttendanceDetail.php |
  | 14 | all | Browser | AdminStaffListTest.php |
  | 15 | all | Feature | AdminApproveModificationTest.php |
  | 16 | 最初<br>最初以外 | Feature<br>Browser | UserRegistrationTest.php<br>EmailVerificationTest.php |

</details>

## URL
- 開発環境 : [http://localhost/](http://localhost/ )
- 一般ユーザー登録 : [http://localhost/register/](http://localhost/register)
- 一般ユーザーログイン : [http://localhost/login/](http://localhost/login)
- 管理ユーザーログイン : [http://localhost/admin/login](http://localhost/admin/login)
- phpMyAdmin : [http://localhost:8080/](http://localhost:8080/)
- MailHog : [http://localhost:8025/](http://localhost:8025/)
