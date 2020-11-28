# Contribution Guidelines

## Introduction

This document explains how to contribute changes to the Phacil Framework project.
It assumes you have followed the
installation instructions on readme file.
Sensitive security-related issues should be reported to
[bug-reports@exactiweb.com](mailto:bug-reports@exactiweb.com) or on our website [exacti.com.br/contato](https://www.exacti.com.br/contato).


## Bug reports

Please search the issues on the issue tracker with a variety of keywords to ensure your bug is not already reported.

If unique, [open an issue](https://github.com/exacti/phacil-framework/issues/new) and answer the questions so we can understand and reproduce the problematic behavior.

To show us that the issue you are having is in Phacil itself, please write clear, concise instructions so we can reproduce the behavior, even if it seems obvious. The more detailed and specific you are, the faster we can fix the issue. Check out [How to Report Bugs Effectively](http://www.chiark.greenend.org.uk/~sgtatham/bugs.html) article.

Even though it is maintained by a company of Brazilian origin, the chosen language for issue reports is English.

**Don't forget**: *maintain your code compatibility at base version and the max PHP version described in README.md file*.

Please be kind, remember that Phacil Framework comes at no cost to you, and  you're getting free help.


## Code review

Changes to Phacil must be reviewed before they are accepted, no matter who makes the change, even if they are an owner or a maintainer. We use GitHub's pull request workflow to do that. 

Please try to make your pull request easy to review for us. And, please read the *[How to get faster PR reviews](https://github.com/kubernetes/community/blob/261cb0fd089b64002c91e8eddceebf032462ccd6/contributors/guide/pull-requests.md#best-practices-for-faster-reviews)* guide. It has lots of useful tips for any project you may want to contribute.

Some of the key points:

* Make small pull requests. The smaller, the faster to review and the more likely it will be merged soon.
* Don't make changes unrelated to your PR. Maybe there are typos on some comments, maybe refactoring would be welcome on a function... but if that is not related to your PR, please make *another* PR for that.
* Split big pull requests into multiple small ones. An incremental change will be faster to review than a huge PR.


## Languages

All repository comunications work only with English language. 


## Add features

If you contribute with a new feature, few free to explain how to use in README.md file. This isn't required but we will take into consideration.

Please, describe better as you can the new feature in the pull request.

Changes on license file inst's allow (even  intentional).


## Versions

We at ExacTI determined the versioning value of this project in the major.minor.fix system.

If your changes is just a bugfix, we increment a +1 in fix version. If your add some feature or change a behavior, we add +1 in minor value. Only for big changes in the struct of Phacil Framework we can consider to change major value, but it's rare for now. 

Plans for major increments are communicated months in advance to prepare a direction and help contributors understand the way that the project will follow.

The versions changes are decided by ExacTI team. The file VERSION in system/engine can be changed like a suggestion, but the ExacTI team can be modify for the value most appropriate.


## Copyright

Code that you contribute should use the standard copyright header:

```php
/**
 * Copyright (c) 2019. ExacTI Technology Solutions
 * GPLv3 General License.
 * https://exacti.com.br
 * Phacil PHP Framework - https://github.com/exacti/phacil-framework
 * Author: YOUR NAME HERE <your@email.com>
 */
```
You also can add your GitHub profile link bellow the author line.

Files in the repository contain copyright from the year they are  added to the year they are last changed. If the copyright author is changed, just paste the header below the old one.