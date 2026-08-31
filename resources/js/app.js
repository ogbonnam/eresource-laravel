import { Editor } from '@tiptap/core';
import StarterKit from '@tiptap/starter-kit';
import Image from '@tiptap/extension-image';
import TextAlign from '@tiptap/extension-text-align';
import Link from '@tiptap/extension-link';
import Underline from '@tiptap/extension-underline';
import { Table } from '@tiptap/extension-table';
import { TableRow } from '@tiptap/extension-table-row';
import { TableHeader } from '@tiptap/extension-table-header';
import { TableCell } from '@tiptap/extension-table-cell';

import "./student-assignment-editor";


/*
|--------------------------------------------------------------------------
| Tiptap Resource Editor
|--------------------------------------------------------------------------
*/

document.addEventListener('DOMContentLoaded', () => {

    const editorElement = document.querySelector('#resource-editor');

    /*
     * If this page doesn't contain a resource editor,
     * simply do nothing.
     */
    if (!editorElement) {
        return;
    }


    const form = document.querySelector('#resource-form');

    const contentInput =
        document.querySelector('#resource-content');

    const imageUploadUrl =
        editorElement.dataset.imageUploadUrl;

    const csrfToken =
        document
            .querySelector('meta[name="csrf-token"]')
            ?.getAttribute('content');


    /*
    |--------------------------------------------------------------------------
    | Create Editor
    |--------------------------------------------------------------------------
    */

    const editor = new Editor({

        element: editorElement,

        editable: true,

        extensions: [

            /*
             * Basic formatting.
             */
            StarterKit.configure({
                heading: {
                    levels: [1, 2, 3],
                },
            }),


            /*
             * Underline.
             */
            Underline,


            /*
             * Links.
             */
            Link.configure({
                openOnClick: false,
                autolink: true,
                linkOnPaste: true,
            }),


            /*
             * Images.
             */
            Image.configure({
                inline: false,
                allowBase64: false,
            }),


            /*
             * Text alignment.
             */
            TextAlign.configure({
                types: [
                    'heading',
                    'paragraph',
                ],
            }),


            /*
             * Tables.
             */
            Table.configure({
                resizable: true,
            }),

            TableRow,

            TableHeader,

            TableCell,
        ],


        /*
         * Existing content.
         */
        content: contentInput?.value || '<p></p>',


        /*
         * Every time the editor changes,
         * update the hidden Laravel input.
         */
        onUpdate: ({ editor }) => {

            if (contentInput) {

                contentInput.value =
                    editor.getHTML();

            }

        },

    });


    /*
    |--------------------------------------------------------------------------
    | Make sure the hidden input contains current content.
    |--------------------------------------------------------------------------
    */

    if (contentInput) {

        contentInput.value =
            editor.getHTML();

    }


    /*
    |--------------------------------------------------------------------------
    | Toolbar helper
    |--------------------------------------------------------------------------
    */

    const command = (callback) => {

        callback(editor);

        editor
            .chain()
            .focus()
            .run();

    };


    /*
    |--------------------------------------------------------------------------
    | Toolbar buttons
    |--------------------------------------------------------------------------
    */

    document
        .querySelectorAll('[data-editor-command]')
        .forEach((button) => {

            button.addEventListener('click', (event) => {

                event.preventDefault();

                const action =
                    button.dataset.editorCommand;


                switch (action) {

                    /*
                    |--------------------------------------------------------------------------
                    | Bold
                    |--------------------------------------------------------------------------
                    */

                    case 'bold':

                        editor
                            .chain()
                            .focus()
                            .toggleBold()
                            .run();

                        break;


                    /*
                    |--------------------------------------------------------------------------
                    | Italic
                    |--------------------------------------------------------------------------
                    */

                    case 'italic':

                        editor
                            .chain()
                            .focus()
                            .toggleItalic()
                            .run();

                        break;


                    /*
                    |--------------------------------------------------------------------------
                    | Underline
                    |--------------------------------------------------------------------------
                    */

                    case 'underline':

                        editor
                            .chain()
                            .focus()
                            .toggleUnderline()
                            .run();

                        break;


                    /*
                    |--------------------------------------------------------------------------
                    | Strike
                    |--------------------------------------------------------------------------
                    */

                    case 'strike':

                        editor
                            .chain()
                            .focus()
                            .toggleStrike()
                            .run();

                        break;


                    /*
                    |--------------------------------------------------------------------------
                    | Heading 1
                    |--------------------------------------------------------------------------
                    */

                    case 'heading1':

                        editor
                            .chain()
                            .focus()
                            .toggleHeading({
                                level: 1,
                            })
                            .run();

                        break;


                    /*
                    |--------------------------------------------------------------------------
                    | Heading 2
                    |--------------------------------------------------------------------------
                    */

                    case 'heading2':

                        editor
                            .chain()
                            .focus()
                            .toggleHeading({
                                level: 2,
                            })
                            .run();

                        break;


                    /*
                    |--------------------------------------------------------------------------
                    | Heading 3
                    |--------------------------------------------------------------------------
                    */

                    case 'heading3':

                        editor
                            .chain()
                            .focus()
                            .toggleHeading({
                                level: 3,
                            })
                            .run();

                        break;


                    /*
                    |--------------------------------------------------------------------------
                    | Paragraph
                    |--------------------------------------------------------------------------
                    */

                    case 'paragraph':

                        editor
                            .chain()
                            .focus()
                            .setParagraph()
                            .run();

                        break;


                    /*
                    |--------------------------------------------------------------------------
                    | Bullet List
                    |--------------------------------------------------------------------------
                    */

                    case 'bulletList':

                        editor
                            .chain()
                            .focus()
                            .toggleBulletList()
                            .run();

                        break;


                    /*
                    |--------------------------------------------------------------------------
                    | Ordered List
                    |--------------------------------------------------------------------------
                    */

                    case 'orderedList':

                        editor
                            .chain()
                            .focus()
                            .toggleOrderedList()
                            .run();

                        break;


                    /*
                    |--------------------------------------------------------------------------
                    | Blockquote
                    |--------------------------------------------------------------------------
                    */

                    case 'blockquote':

                        editor
                            .chain()
                            .focus()
                            .toggleBlockquote()
                            .run();

                        break;


                    /*
                    |--------------------------------------------------------------------------
                    | Align Left
                    |--------------------------------------------------------------------------
                    */

                    case 'alignLeft':

                        editor
                            .chain()
                            .focus()
                            .setTextAlign('left')
                            .run();

                        break;


                    /*
                    |--------------------------------------------------------------------------
                    | Align Center
                    |--------------------------------------------------------------------------
                    */

                    case 'alignCenter':

                        editor
                            .chain()
                            .focus()
                            .setTextAlign('center')
                            .run();

                        break;


                    /*
                    |--------------------------------------------------------------------------
                    | Align Right
                    |--------------------------------------------------------------------------
                    */

                    case 'alignRight':

                        editor
                            .chain()
                            .focus()
                            .setTextAlign('right')
                            .run();

                        break;


                    /*
                    |--------------------------------------------------------------------------
                    | Link
                    |--------------------------------------------------------------------------
                    */

                    case 'link':

                        addLink(editor);

                        break;


                    /*
                    |--------------------------------------------------------------------------
                    | Image
                    |--------------------------------------------------------------------------
                    */

                    case 'image':

                        chooseImage();

                        break;


                    /*
                    |--------------------------------------------------------------------------
                    | Table
                    |--------------------------------------------------------------------------
                    */

                    case 'table':

                        editor
                            .chain()
                            .focus()
                            .insertTable({
                                rows: 3,
                                cols: 3,
                                withHeaderRow: true,
                            })
                            .run();

                        break;


                    /*
                    |--------------------------------------------------------------------------
                    | Undo
                    |--------------------------------------------------------------------------
                    */

                    case 'undo':

                        editor
                            .chain()
                            .focus()
                            .undo()
                            .run();

                        break;


                    /*
                    |--------------------------------------------------------------------------
                    | Redo
                    |--------------------------------------------------------------------------
                    */

                    case 'redo':

                        editor
                            .chain()
                            .focus()
                            .redo()
                            .run();

                        break;

                }

            });

        });


    /*
    |--------------------------------------------------------------------------
    | Link
    |--------------------------------------------------------------------------
    */

    function addLink(editor) {

        const previousUrl =
            editor.getAttributes('link').href || '';


        const url = window.prompt(
            'Enter URL',
            previousUrl
        );


        if (url === null) {
            return;
        }


        if (url === '') {

            editor
                .chain()
                .focus()
                .extendMarkRange('link')
                .unsetLink()
                .run();

            return;
        }


        editor
            .chain()
            .focus()
            .extendMarkRange('link')
            .setLink({
                href: url,
                target: '_blank',
            })
            .run();

    }


    /*
    |--------------------------------------------------------------------------
    | Image picker
    |--------------------------------------------------------------------------
    */

    function chooseImage() {

        const input =
            document.createElement('input');


        input.type = 'file';

        input.accept =
            'image/png,image/jpeg,image/jpg,image/gif,image/webp';


        input.addEventListener(
            'change',
            async () => {

                const file =
                    input.files?.[0];


                if (!file) {
                    return;
                }


                await uploadImage(file);

            }
        );


        input.click();

    }


    /*
    |--------------------------------------------------------------------------
    | Upload image
    |--------------------------------------------------------------------------
    */

    async function uploadImage(file) {

        if (!imageUploadUrl) {

            alert(
                'Image upload endpoint is not configured.'
            );

            return;
        }


        if (!file.type.startsWith('image/')) {

            alert(
                'Please select an image.'
            );

            return;
        }


        if (file.size > 5 * 1024 * 1024) {

            alert(
                'Image must be smaller than 5MB.'
            );

            return;
        }


        const formData =
            new FormData();


        formData.append(
            'image',
            file
        );


        try {

            const response =
                await fetch(
                    imageUploadUrl,
                    {
                        method: 'POST',

                        headers: {
                            'X-CSRF-TOKEN':
                                csrfToken,

                            'Accept':
                                'application/json',
                        },

                        body: formData,
                    }
                );


            if (!response.ok) {

                throw new Error(
                    'Image upload failed.'
                );

            }


            const data =
                await response.json();


            if (!data.url) {

                throw new Error(
                    'Image URL was not returned.'
                );

            }


            editor
                .chain()
                .focus()
                .setImage({
                    src: data.url,
                })
                .run();

        } catch (error) {

            console.error(error);

            alert(
                'Unable to upload image. Please try again.'
            );

        }

    }


    /*
    |--------------------------------------------------------------------------
    | Paste images
    |--------------------------------------------------------------------------
    */

    editorElement.addEventListener(
        'paste',
        async (event) => {

            const items =
                event.clipboardData?.items;


            if (!items) {
                return;
            }


            for (const item of items) {

                if (!item.type.startsWith('image/')) {
                    continue;
                }


                const file =
                    item.getAsFile();


                if (!file) {
                    continue;
                }


                /*
                 * Stop the browser from inserting
                 * the raw clipboard image.
                 */
                event.preventDefault();


                await uploadImage(file);

                return;

            }

        }
    );


    /*
    |--------------------------------------------------------------------------
    | Drag and drop images
    |--------------------------------------------------------------------------
    */

    editorElement.addEventListener(
        'drop',
        async (event) => {

            const files =
                event.dataTransfer?.files;


            if (!files?.length) {
                return;
            }


            const image =
                Array
                    .from(files)
                    .find(file =>
                        file.type.startsWith('image/')
                    );


            if (!image) {
                return;
            }


            event.preventDefault();


            await uploadImage(image);

        }
    );


    /*
    |--------------------------------------------------------------------------
    | Submit
    |--------------------------------------------------------------------------
    */

    if (form) {

        form.addEventListener(
            'submit',
            () => {

                if (contentInput) {

                    contentInput.value =
                        editor.getHTML();

                }

            }
        );

    }


    /*
    |--------------------------------------------------------------------------
    | Expose editor for debugging
    |--------------------------------------------------------------------------
    */

    window.resourceEditor = editor;


    /*
    |--------------------------------------------------------------------------
    | Debug
    |--------------------------------------------------------------------------
    */

    console.log(
        'TipTap resource editor initialized.',
        editor
    );

});


/*
|--------------------------------------------------------------------------
| Resource Type UI
|--------------------------------------------------------------------------
*/

document.addEventListener('DOMContentLoaded', () => {

    const typeSelect =
        document.querySelector('#type');


    const editorSection =
        document.querySelector('#editor-section');


    const fileSection =
        document.querySelector('#file-section');


    if (
        !typeSelect ||
        !editorSection ||
        !fileSection
    ) {
        return;
    }


    function updateResourceInterface() {

        const type =
            typeSelect.value;


        const richContentTypes = [
            'lesson_note',
            'assignment',
        ];


        const isRichContent =
            richContentTypes.includes(type);


        /*
        |--------------------------------------------------------------------------
        | Rich content resources
        |--------------------------------------------------------------------------
        */

        if (isRichContent) {

            editorSection.classList.remove(
                'hidden'
            );

        } else {

            editorSection.classList.add(
                'hidden'
            );

        }


        /*
        |--------------------------------------------------------------------------
        | File section
        |--------------------------------------------------------------------------
        */

        const fileTypes = [
            'pdf',
            'document',
            'video',
            'zip',
        ];


        if (fileTypes.includes(type)) {

            fileSection.classList.remove(
                'hidden'
            );

        } else {

            fileSection.classList.add(
                'hidden'
            );

        }

    }


    typeSelect.addEventListener(
        'change',
        updateResourceInterface
    );


    updateResourceInterface();

});




/*
|--------------------------------------------------------------------------
| Tiptap Assignment Editor
|--------------------------------------------------------------------------
|
| The assignment editor uses the same Tiptap installation as the
| resource editor, but has its own DOM selectors and toolbar commands.
|
*/

document.addEventListener('DOMContentLoaded', () => {

    const editorElement =
        document.querySelector('#assignment-editor');

    /*
     * This page does not contain an assignment editor.
     */
    if (!editorElement) {
        return;
    }

    const form =
        editorElement.closest('form');

    const contentInput =
        document.querySelector('#assignment-instructions');


    /*
    |--------------------------------------------------------------------------
    | Create Assignment Editor
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
         * Existing assignment instructions.
         */
        content:
            contentInput?.value ||
            '<p></p>',

        /*
         * Keep Laravel hidden field synchronized.
         */
        onUpdate: ({ editor }) => {

            if (contentInput) {

                contentInput.value =
                    editor.getHTML();

            }

        },

    });


    /*
    |--------------------------------------------------------------------------
    | Initial hidden field value
    |--------------------------------------------------------------------------
    */

    if (contentInput) {

        contentInput.value =
            editor.getHTML();

    }


    /*
    |--------------------------------------------------------------------------
    | Assignment toolbar
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

                        /*
                        |--------------------------------------------------------------------------
                        | Paragraph
                        |--------------------------------------------------------------------------
                        */

                        case 'paragraph':

                            editor
                                .chain()
                                .focus()
                                .setParagraph()
                                .run();

                            break;


                        /*
                        |--------------------------------------------------------------------------
                        | Heading 1
                        |--------------------------------------------------------------------------
                        */

                        case 'heading1':

                            editor
                                .chain()
                                .focus()
                                .toggleHeading({
                                    level: 1,
                                })
                                .run();

                            break;


                        /*
                        |--------------------------------------------------------------------------
                        | Heading 2
                        |--------------------------------------------------------------------------
                        */

                        case 'heading2':

                            editor
                                .chain()
                                .focus()
                                .toggleHeading({
                                    level: 2,
                                })
                                .run();

                            break;


                        /*
                        |--------------------------------------------------------------------------
                        | Heading 3
                        |--------------------------------------------------------------------------
                        */

                        case 'heading3':

                            editor
                                .chain()
                                .focus()
                                .toggleHeading({
                                    level: 3,
                                })
                                .run();

                            break;


                        /*
                        |--------------------------------------------------------------------------
                        | Bold
                        |--------------------------------------------------------------------------
                        */

                        case 'bold':

                            editor
                                .chain()
                                .focus()
                                .toggleBold()
                                .run();

                            break;


                        /*
                        |--------------------------------------------------------------------------
                        | Italic
                        |--------------------------------------------------------------------------
                        */

                        case 'italic':

                            editor
                                .chain()
                                .focus()
                                .toggleItalic()
                                .run();

                            break;


                        /*
                        |--------------------------------------------------------------------------
                        | Underline
                        |--------------------------------------------------------------------------
                        */

                        case 'underline':

                            editor
                                .chain()
                                .focus()
                                .toggleUnderline()
                                .run();

                            break;


                        /*
                        |--------------------------------------------------------------------------
                        | Bullet List
                        |--------------------------------------------------------------------------
                        */

                        case 'bulletList':

                            editor
                                .chain()
                                .focus()
                                .toggleBulletList()
                                .run();

                            break;


                        /*
                        |--------------------------------------------------------------------------
                        | Ordered List
                        |--------------------------------------------------------------------------
                        */

                        case 'orderedList':

                            editor
                                .chain()
                                .focus()
                                .toggleOrderedList()
                                .run();

                            break;


                        /*
                        |--------------------------------------------------------------------------
                        | Blockquote
                        |--------------------------------------------------------------------------
                        */

                        case 'blockquote':

                            editor
                                .chain()
                                .focus()
                                .toggleBlockquote()
                                .run();

                            break;


                        /*
                        |--------------------------------------------------------------------------
                        | Align Left
                        |--------------------------------------------------------------------------
                        */

                        case 'alignLeft':

                            editor
                                .chain()
                                .focus()
                                .setTextAlign('left')
                                .run();

                            break;


                        /*
                        |--------------------------------------------------------------------------
                        | Align Center
                        |--------------------------------------------------------------------------
                        */

                        case 'alignCenter':

                            editor
                                .chain()
                                .focus()
                                .setTextAlign('center')
                                .run();

                            break;


                        /*
                        |--------------------------------------------------------------------------
                        | Align Right
                        |--------------------------------------------------------------------------
                        */

                        case 'alignRight':

                            editor
                                .chain()
                                .focus()
                                .setTextAlign('right')
                                .run();

                            break;


                        /*
                        |--------------------------------------------------------------------------
                        | Link
                        |--------------------------------------------------------------------------
                        */

                        case 'link':

                            addAssignmentLink(editor);

                            break;


                        /*
                        |--------------------------------------------------------------------------
                        | Undo
                        |--------------------------------------------------------------------------
                        */

                        case 'undo':

                            editor
                                .chain()
                                .focus()
                                .undo()
                                .run();

                            break;


                        /*
                        |--------------------------------------------------------------------------
                        | Redo
                        |--------------------------------------------------------------------------
                        */

                        case 'redo':

                            editor
                                .chain()
                                .focus()
                                .redo()
                                .run();

                            break;

                    }

                }
            );

        });


    /*
    |--------------------------------------------------------------------------
    | Link helper
    |--------------------------------------------------------------------------
    */

    function addAssignmentLink(editor) {

        const previousUrl =
            editor
                .getAttributes('link')
                .href || '';


        const url =
            window.prompt(
                'Enter URL',
                previousUrl
            );


        if (url === null) {
            return;
        }


        /*
         * Remove existing link.
         */
        if (url === '') {

            editor
                .chain()
                .focus()
                .extendMarkRange('link')
                .unsetLink()
                .run();

            return;
        }


        editor
            .chain()
            .focus()
            .extendMarkRange('link')
            .setLink({
                href: url,
                target: '_blank',
            })
            .run();

    }


    /*
    |--------------------------------------------------------------------------
    | Submit
    |--------------------------------------------------------------------------
    */

    if (form) {

        form.addEventListener(
            'submit',
            () => {

                if (contentInput) {

                    contentInput.value =
                        editor.getHTML();

                }

            }
        );

    }


    /*
    |--------------------------------------------------------------------------
    | Expose for debugging
    |--------------------------------------------------------------------------
    */

    window.assignmentEditor =
        editor;


    console.log(
        'Tiptap assignment editor initialized.',
        editor
    );

});