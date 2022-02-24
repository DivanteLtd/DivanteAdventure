# Contribution

If you're already a Vue/Symfony developer and want to become a member of contributors community, thanks for your
interest! Read this contribution guide, pick an issue from GitHub and push a pull request. If you're a newcomer, try
looking for "good first issue" tags, they should be easier to implement.

## Branches

We are using following development branches:
* `master` - contains the newest stable release of the app and can be used on production environment. Create your
    feature branches from here! This is deployed as a main "demo" instance.
* `develop` - the most recent version of app, which can contain features that - while finished and tested - may
    still not be fully stable. This is deployed as a secondary "nightly demo" instance.
* `testing` - this is an unstable branch used by manual testers to verify tasks. In merge request you should merge
    your feature branches here.
* `release/X.Y.Z` (X.Y.Z is a future version) - these branches are created from master and contain all fully tested
    features that already went through `testing` and `develop` and are considered fully stable. That branch will be
    merged into `master` to create a new version.
* `feature/X`; `bugfix/X` (X is issue ID with short description) - branches containing changes for a single issue. These
    are the main branches for most contributors. For example, if you want to create a solution for issue "#5000
    Implement an AI for automatic hiring and firing people based on the last letter of their surname", create a branch
    `feature/5000-ai-for-hiring-and-firing` from `master` and a merge request to `testing`.

## Versioning

After many tries of keeping [semantic versioning](https://semver.org/) and defining which change is small and which
can be considered incompatible, we decided to change versioning to less semantic and more date-based, which major number
representing last two digits of a year<sup>1</sup>, minor number representing month with leading zero (from 01 to 12)
and patch number incrementing with every release, resetting to 1 at the beginning of a year.

For example, version "20.06.5" means "fifth release in June 2020".

###### Notes
<sup>1</sup> - There is an incoming problem of resetting a counter after year 2100. We'll try to address that somewhere
around 2099.

## Pull request checklist

Following criteria must be met before merging a pull request:
* PR is proposed to appropriate branch and merges to `testing`
* PR fully or partially solves a corresponding issue and doesn't change anything outside the scope of the issue
* Try to create an automatic tests in PHPUnit/Jest that will verify correct working of your feature or in case of a
    bugfix verifies that such a bug is not happening anymore
* The CI pipeline for a PR is finished successfully without any errors
* Code review is successfully passed and PR got at least two approvals from core team members.

# Issues and features

## Potential security issues

If you have found a potential security issue, please do NOT report it in public issue tracker, instead send it via 
e-mail to <tu wstawimy adres e-mail>. Please provide the following information:
* Which part of application is affected?
* How to reproduce the issue?
* If possible, an idea how to fix the issue.

## Other issues

If you have found a bug which does not concern security, please create a new issue on GitHub with "bug" tag and provide
the following information:
* Which part of application is affected?
* How to reproduce the issue?
* If possible, an idea how to fix the issue.

## Feature proposals
If you have any ideas of new features that are lacking from application, please create a new issue on GitHub with
"feature request" tag and provide the following information:
* How this feature should work and look like?
* Why do you believe it should be a part of application?
* How could it affect other parts of application, in both positive and negative way?
