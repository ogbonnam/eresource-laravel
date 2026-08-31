import { Editor } from '@tiptap/core';
import StarterKit from '@tiptap/starter-kit';
import Link from '@tiptap/extension-link';
import Underline from '@tiptap/extension-underline';
import TextAlign from '@tiptap/extension-text-align';


/*
|--------------------------------------------------------------------------
| Student Assignment Tiptap Editor
|--------------------------------------------------------------------------
*/

document.addEventListener('DOMContentLoaded', () => {

    /*
    |--------------------------------------------------------------------------
    | Find editor
    |--------------------------------------------------------------------------
    */

    const editorElement =
        document.querySelector('#assignment-editor');


    /*
    |--------------------------------------------------------------------------
    | This page does not contain the assignment editor.
    |--------------------------------------------------------------------------
    */

    if (!editorElement) {
        return;
    }


    /*
    |--------------------------------------------------------------------------
    | Find form
    |--------------------------------------------------------------------------
    */

    const form =
        document.querySelector('#assignment-form');


    /*
    |--------------------------------------------------------------------------
    | Hidden content input
    |--------------------------------------------------------------------------
    */

    const contentInput =
        document.querySelector('#assignment-content');


    /*
    |--------------------------------------------------------------------------
    | Safety check
    |--------------------------------------------------------------------------
    */

    if (!contentInput) {

        console.error(
            'Assignment editor: #assignment-content was not found.'
        );

        return;
    }


    /*
    |--------------------------------------------------------------------------
    | Existing content
    |--------------------------------------------------------------------------
    */

    const existingContent =
        contentInput.value?.trim() || '<p></p>';


    /*
    |--------------------------------------------------------------------------
    | Create editor
    |--------------------------------------------------------------------------
    */

    const editor = new Editor({

        element: editorElement,

        editable: true,

        extensions: [

            StarterKit.configure({

                heading: {
                    levels: [1, 2, 3],
                },

            }),


            Underline,


            Link.configure({

                openOnClick: false,

                autolink: true,

                linkOnPaste: true,

            }),


            TextAlign.configure({

                types: [
                    'heading',
                    'paragraph',
                ],

            }),

        ],


        /*
        |--------------------------------------------------------------------------
        | Load saved draft
        |--------------------------------------------------------------------------
        */

        content: existingContent,


        /*
        |--------------------------------------------------------------------------
        | Tiptap changed
        |--------------------------------------------------------------------------
        */

        onUpdate: ({ editor }) => {

            syncEditorToInput(editor);

        },

    });


    /*
    |--------------------------------------------------------------------------
    | Synchronize Tiptap → hidden input
    |--------------------------------------------------------------------------
    */

    function syncEditorToInput(editorInstance = editor) {

        if (!contentInput) {
            return;
        }


        const html =
            editorInstance.getHTML();


        contentInput.value =
            html;


        /*
        |--------------------------------------------------------------------------
        | Debug
        |--------------------------------------------------------------------------
        */

        console.log(
            'Assignment content synchronized:',
            html
        );

    }


    /*
    |--------------------------------------------------------------------------
    | Initial synchronization
    |--------------------------------------------------------------------------
    */

    syncEditorToInput(editor);


    /*
    |--------------------------------------------------------------------------
    | Toolbar
    |--------------------------------------------------------------------------
    */

    document
        .querySelectorAll('[data-assignment-command]')
        .forEach((button) => {

            button.addEventListener(
                'click',
                (event) => {

                    event.preventDefault();


                    const action =
                        button.dataset.assignmentCommand;


                    switch (action) {

                        case 'bold':

                            editor
                                .chain()
                                .focus()
                                .toggleBold()
                                .run();

                            break;


                        case 'italic':

                            editor
                                .chain()
                                .focus()
                                .toggleItalic()
                                .run();

                            break;


                        case 'underline':

                            editor
                                .chain()
                                .focus()
                                .toggleUnderline()
                                .run();

                            break;


                        case 'bulletList':

                            editor
                                .chain()
                                .focus()
                                .toggleBulletList()
                                .run();

                            break;


                        case 'orderedList':

                            editor
                                .chain()
                                .focus()
                                .toggleOrderedList()
                                .run();

                            break;


                        case 'heading2':

                            editor
                                .chain()
                                .focus()
                                .toggleHeading({
                                    level: 2,
                                })
                                .run();

                            break;


                        case 'undo':

                            editor
                                .chain()
                                .focus()
                                .undo()
                                .run();

                            break;


                        case 'redo':

                            editor
                                .chain()
                                .focus()
                                .redo()
                                .run();

                            break;

                    }


                    /*
                    |--------------------------------------------------------------------------
                    | Synchronize after toolbar action
                    |--------------------------------------------------------------------------
                    */

                    syncEditorToInput(editor);

                }

            );

        });


    /*
    |--------------------------------------------------------------------------
    | Submit / Save Draft
    |--------------------------------------------------------------------------
    |
    | Both buttons use the same form.
    |
    | The only difference is:
    |
    | Submit:
    |   /student/assignments/{assignment}
    |
    | Save Draft:
    |   /student/assignments/{assignment}/draft
    |
    | Blade's formaction attribute handles the URL.
    |
    | JavaScript only synchronizes the content.
    |
    */

    if (form) {

        form.addEventListener(
            'submit',
            () => {

                /*
                |--------------------------------------------------------------------------
                | ALWAYS synchronize immediately before request.
                |--------------------------------------------------------------------------
                */

                syncEditorToInput(editor);


                console.log(
                    'Assignment form submitting:',
                    contentInput.value
                );

            }
        );

    }


    /*
    |--------------------------------------------------------------------------
    | Expose editor for debugging
    |--------------------------------------------------------------------------
    */

    window.assignmentEditor =
        editor;


    console.log(
        'Student assignment Tiptap editor initialized.',
        editor
    );

});