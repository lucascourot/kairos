# Monorepo vs Polyrepo

## Context and Problem Statement

Our project involves developing three major categories of software:

  * Front-end GUIs
  * Back-end servers

When we develop, our source code management (SCM) version control system (VCS) is git.

We need to choose how we use git to organize our code.

## Considered Options

  * Monorepo means we put all pieces into one big repo
  * Polyrepo means we put each piece in its own repo
  * Hybrid means some mix of monorepo and polyrepo

## Decision Outcome

Chosen option: "Monorepo", because rapid iteration and ease of refactoring is a higher priority.
Scaling issues will not arise in the short to middle term.
Splitting one repo is easier than combining multiple repos.
Google and Facebook are very strong advocates for monorepos over polyrepos, because all the core offerings can be developed/tested/deployed in concert. 

## Links

For more please see https://github.com/joelparkerhenderson/monorepo_vs_polyrepo
