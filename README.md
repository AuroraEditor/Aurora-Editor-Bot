<p align="center">
  <img alt="Logo" src="https://avatars.githubusercontent.com/u/106490518?s=128&v=4" width="128px;" height="128px;">
</p>

<p align="center">
  <h1 align="center">Aurora Editor GitHub Webhook</h1>
</p>

<p align="center">
  <a href='https://twitter.com/Aurora_Editor' target='_blank'>
    <img alt="Twitter Follow" src="https://img.shields.io/twitter/follow/Aurora_Editor?color=f6579d&style=for-the-badge">
  </a>
  <a href='https://discord.gg/5aecJ4rq9D' target='_blank'>
    <img alt="Discord" src="https://img.shields.io/discord/997410333348077620?color=f98a6c&style=for-the-badge">
  </a>
  <a href='https://twitter.com/intent/tweet?text=Try%20this%20new%20open-source%20code%20editor,%20Aurora%20Editor&url=https://auroraeditor.com&via=Aurora_Editor&hashtags=AuroraEditor,editor,AEIDE,developers,Aurora,OSS' target='_blank'><img src='https://img.shields.io/twitter/url/http/shields.io.svg?style=social'></a>
</p>

<br />

This is the repository for the Aurora Editor GitHub Webhook bot.<br/>
The bot is used to automate some tasks in the AuroraEditor organization.
- [Automatically add the PR creator as a assignee & set the correct milestone](action/open-pr.php).
- [Respond on a issue with a comment](action/respond-issue.php)
- [Review on request](action/react-comment.php).

## Run 

create a config.php file in the root directory with the following contents:

```php
<?php
$settings = array(
    "username" => "my-bot-name",
    "admins" => array(
        "0xWDG",
        "my-bot-name"
    ),
    "github_token" => "GITHUB TOKEN",
    "discord_webhook" => "DISCORD WEBHOOK",
    "serverURL" => "Https url for debugging"
);
```

> **Note**\
> Change all values to your own settins/values.

# Run the webhook
Upload this to your server and serve it with PHP. 

# Tests

This section tells you how to test different payloads.

> **Note**\
> Some payloads may not work in your environment.<br/>
> if you have not the correct rights to [AuroraEditor/Aurora-Editor-Bot](https://github.com/AuroraEditor/Aurora-Editor-Bot).

### Run all tests
```bash
sh test_all_payloads.sh
```

#### Run test (issue.json)
This simulates a new issue [Aurora-Editor-Bot #24](https://github.com/AuroraEditor/Aurora-Editor-Bot/issues/24).
```bash
php index.php issue.json
```


#### Run test (please_assign_me.json)
This simulates a new comment in [Aurora-Editor-Bot #24](https://github.com/AuroraEditor/Aurora-Editor-Bot/issues/24) saying `@aurora-editor-bot please assign me`.
```bash
php index.php please_assign_me.json
```
          

#### Run test (pr.json)
This simulates a new PR [Aurora-Editor-Bot #25](https://github.com/AuroraEditor/Aurora-Editor-Bot/pull/25).
```bash
php index.php pr.json
```


#### Run test (pr_reject.json)
This simulates a new comment in [Aurora-Editor-Bot #25](https://github.com/AuroraEditor/Aurora-Editor-Bot/pull/25) from an admin saying `@aurora-editor-bot please reject this pr.`.

> **Warning**\
> This will not execute if `my-bot-name` is not an admin.

```bash
php index.php pr_reject.json
```


#### Run test (pr_accept.json)
This simulates a new comment in [Aurora-Editor-Bot #25](https://github.com/AuroraEditor/Aurora-Editor-Bot/pull/25) from an admin saying `@aurora-editor-bot please accept this pr.`.

> **Warning**\
> This will not execute if `my-bot-name` is not an admin.

```bash
php index.php pr_accept.json
```


#### Run test (pr_closed.json)
This simulates the [Aurora-Editor-Bot #25](https://github.com/AuroraEditor/Aurora-Editor-Bot/pull/25) being closed without merge, this should not trigger any action.

```bash
php index.php pr_closed.json
```


#### Run test (pr_merged.json)
This simulates the [Aurora-Editor-Bot #25](https://github.com/AuroraEditor/Aurora-Editor-Bot/pull/25) being merged.

```bash
php index.php pr_merged.json
```