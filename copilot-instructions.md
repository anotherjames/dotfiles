- Prefer writing PHP if no language is specified.
- Use npm for nodeJS dependencies
- Respond with terse language, but try to be friendly within that.
- Prefer being correct over offering any response you're unsure of, allow yourself to say: "I don't know."
- Keep consistency with approaches and coding style in existing custom code
- Avoid unnecessary changes. Ensure changes to existing pull requests will keep the overall diff of a PR to only change what's necessary.
- Long-term maintenance and future extensibility are important. For example, any new settings or administrative forms should be useful enough to be extensible in future for other related settings.
- Any scripts created to complete a task should be kept in the repo root, for inspection and potential future use. Do not just overwrite existing script files when doing something new; make a new script file.

Review all of your code changes yourself to check for unforeseen issues and potential improvements (without unnecessarily increasing scope or functionality), taking time to think like an expert developer (specialising in Drupal, for Drupal projects) who aims for clean code and long term maintainability.

When adding new condition-heavy logic, prefer nested `if` blocks over early returns unless there is a clear project-specific reason to do otherwise.

## Drupal projects

(Requiring `drupal/core` or `drupal/core-recommended` in a root `composer.json` file shows a project uses Drupal.)

- Prefer to work within existing custom modules or the existing custom theme.
- Use Drupal coding standards (documented from https://project.pages.drupalcode.org/coding_standards/)
- DDEV and Drush are common useful tools
- Access checks should respect permissions rather than directly checking user roles.

## Database queries

If `$HOME/.my-ro.cnf` exists, a read-only database user (`copilot_ro`, `SELECT` privileges only) should always be used for running database queries directly:

```
mariadb --defaults-file="$HOME/.my-ro.cnf" -e "SELECT ..."
```

This avoids the risk of accidentally running destructive statements. If `$HOME/.my-ro.cnf` does not exist, fall back to using drush (when available) or `mariadb`/`mysql`.

## Git usage

Commit messages and PR titles should follow the following format, keeping the first line within 72 characters long:

```
CM-12345: Brief summary of solution

Longer description here, with line breaks added manually to wrap the description
across multiple lines, each within 72 characters long.
```

Ask for the ticket number (the 'CM-12345' part) if you haven't been told what it is. Ignore any hash/pound sign (#) in it. New git branches should be prefixed with `feature/12345-` (rather than `copilot/*`).

The titles of PRs should reflect their overall intention, not just the latest commit. So do not change the titles of existing PRs when adding a commit to one, unless a commit significantly changes its overall purpose, or you have been explicitly asked to.

