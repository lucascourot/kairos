# Kairos

## Why?

This is a R&D project developed by WakeOnWeb (WoW). It should be used for practicing new ideas.

## Docs

 * [Architectural Decision Records (ADR)](docs/adr)

## Ubiquitous language

 * Exercise
   * MCQ (Multiple Choice Question(s))
   * Open-Ended Response
 
 * Classroom
 
## Requirements
Ensure you have a postgreSql server running (wow-docker-env is recommended)

## Getting started
- Update your **DATABASE_URL** **API_BASE_URI** in your .env* file 
        
        DATABASE_URL=postgresql://[user]:[password]@[serverIp]:[port]/kairos
        API_BASE_URI=http://127.0.0.1:8000/api
        
- Remember to update your **APP_ENV** variable in .env files if you used any
other configuration file like _.env.test_
- Install project dependencies
        
        composer install
- Start your symfony server

        ./bin/console server:run
        
- Run tests
        
        ./vendor/bin/phpunit
        
